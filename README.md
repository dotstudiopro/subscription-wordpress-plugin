
# dotstudioPRO Subscription Wordpress Plugin

This plugin is an addon to the dotstudioPRO API plugin to allow users to manage their subscriptions.

## Deployments

To deploy this plugin: 

1. Create a PR into staging with your branch to trigger the linter. 
If the linter has no problems, merge the PR into staging.

2. Create a PR from staging to master. This will trigger the Github Action
to run Gulp tasks and sync the updates to S3.

3. After the S3 sync, merge the PR into master. The updater lambda will serve
the most up-to-date master zip file from Github when a site needs to update.

## Validation

During a pull request from a branch into staging,
a PHP linter runs as a Github Action to validate
the scripts we're pushing.

During a pull request from a branch into master or staging,
we run gulp to process the various changes in
JS/CSS/SASS files, at which point those files are
similarly validated.