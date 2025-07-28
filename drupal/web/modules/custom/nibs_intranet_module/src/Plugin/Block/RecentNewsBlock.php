<?php
namespace Drupal\news_articles\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Recent News Articles' block.
 *
 * @Block(
 * id = "recent_news_articles_block",
 * admin_label = @Translation("Recent News Articles"),
 * category = @Translation("News Articles")
 * )
 */
class RecentNewsArticlesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    RouteMatchInterface $route_match
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->routeMatch = $route_match;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_route_match')
    );
  }

  public function build() {
    $articles = [];
    $current_node = $this->routeMatch->getParameter('node');
    $department_tids = [];

    if ($current_node instanceof NodeInterface && $current_node->getType() === 'news_article') {
      // Get the department IDs
      if (!$current_node->get('field_department')->isEmpty()) {
        foreach ($current_node->get('field_department') as $item) {
          $department_tids[] = $item->target_id;
        }
      }
    }

    if (!empty($department_tids)) {
      try {
        // Load the node storage handler.
        $node_storage = $this->entityTypeManager->getStorage('node');

        // Create a query to find news articles.
        $query = $node_storage->getQuery()
          ->condition('type', 'news_article')
          ->condition('status', NodeInterface::PUBLISHED)
          ->condition('field_department.target_id', $department_tids, 'IN')
          ->sort('created', 'DESC')
          ->range(0, 3);

        // Exclude the current node from the results if it's a full node view.
        if ($current_node instanceof NodeInterface) {
          $query->condition('nid', $current_node->id(), '<>');
        }

        // Execute the query to get node IDs.
        $nids = $query->execute();

        // If news articles are found, load them.
        if (!empty($nids)) {
          $articles = $node_storage->loadMultiple($nids);
        }
      } catch (\Exception $e) {
        // Log the error for debugging.
        \Drupal::logger('news_articles')->error('Error loading recent news articles: @message', ['@message' => $e->getMessage()]);
        // Optionally, return an empty array or a message to the user.
      }
    }

    // Prepare the render array for the block.
    // We use the custom theme hook defined in news_articles.module.
    $build = [
      '#theme' => 'news_articles_recent_block',
      '#articles' => $articles,
      '#cache' => [
        // Cacheability metadata:
        // Cache this block based on the URL (to differentiate between news articles).
        // If the current node changes, the block content might change.
        'contexts' => ['url.path'],
        // Invalidate cache when a 'node' or 'taxonomy_term' entity is updated/deleted.
        // This ensures the block is fresh when news articles or departments change.
        'tags' => Cache::mergeTags($this->getCacheTags(), ['node_list', 'taxonomy_term_list']),
        // Max-age for cache (e.g., 1 hour if no relevant content changes)
        // If you want to ensure it's always fresh on content change, rely on tags.
        'max-age' => Cache::PERMANENT, // Cache permanently until invalidated by tags.
      ],
    ];

    // Add cacheable dependency on the current node.
    // This ensures the block's cache is invalidated if the current node itself changes.
    if ($current_node instanceof NodeInterface) {
      $build['#cache']['tags'] = Cache::mergeTags($build['#cache']['tags'], $current_node->getCacheTags());
      $build['#cache']['contexts'] = Cache::mergeContexts($build['#cache']['contexts'], ['url.path']);
    }

    // Add cacheable dependency on the taxonomy terms used for filtering.
    // If a department term is updated, the block should re-render.
    if (!empty($department_tids)) {
      $build['#cache']['tags'] = Cache::mergeTags($build['#cache']['tags'], Cache::buildTags('taxonomy_term', $department_tids));
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * This method defines additional cache tags for the block.
   * In this case, we want the block to be invalidated if any 'node' of type 'news_article'
   * is created, updated, or deleted.
   */
  public function getCacheTags() {
    // Add the 'node_list:news_article' tag to invalidate when any news article changes.
    return Cache::mergeTags(parent::getCacheTags(), ['node_list:news_article']);
  }

  /**
   * {@inheritdoc}
   *
   * This method defines additional cache contexts for the block.
   * 'url.path' ensures that the block content is cached differently for different URLs.
   * 'user.roles' could be added if content varies by user role.
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }
}
