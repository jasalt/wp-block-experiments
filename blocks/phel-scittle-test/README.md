# Phel Scittle block

Block that uses Phel for SSR with it's Hiccup-like syntax and Scittle with Reagent Hiccup dialect for frontend.

To use this, run `composer install` in the directory.

## Known issues

### Phel should be built ahead of time to not affect performance so much
Phel is transpiled on the fly on every request which slows down page rendering. To build Phel project ahead of time see https://phel-lang.org/documentation/cli-commands/#build-the-project.

### Composer autoloader compatibility with WordPress
Phel relies on Composer autoloader for dependency management.

WordPress does not play well with it, leading to conflicts if autoloader is ran multiple times from different plugins.

To overcome, a scoper tool such as [PHP Scoper](https://github.com/humbug/php-scoper) could be used, see also https://github.com/humbug/php-scoper/blob/main/docs/further-reading.md#wordpress-support.
