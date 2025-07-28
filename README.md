-- Setup instructions for both projects
Drupal - load the drupal dump into a mysql database, the drupal user is amind and the password is adminpassword
Api - run the symfony_etup script to create the database, run composer do the inital migration
php bin/console doctrine:migrations:migrate
This will populate the sample data needed for the api

-- Key design decisions and any trade-offs
Initally wanted to setup within docker containers but ran into issues so I went with a basic setup.
Requires more setup knowledge from the end user

-- Assumptions you made
Assuming basic understanding of LAMP setup for both projects

-- Tools or modules used
For drupal - composer and drush 
For symfony - composer and symfony_cli