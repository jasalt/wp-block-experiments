# ClojureScript / Phel WP Block experiments

TODO 
- documentation is in progress
- blocks/scittle-test works (vanilla php + clojurescript)
- blocks/phel-scittle-test is 75% in progress, does not render fresh attrs (phel server-side rendering + clojurescript)


## What is a WordPress block?
Layout snippet with graphical CMS user interface based on WordPress "Gutenberg" ReactJS editor.

Code:
<!-- wp:my-plugin/scittle-block {"textContent":"FOO text here","isBold":true} /-->
Editor view:
<screenshot>

Javascript editor saves block properties to database.
PHP renders it on server-side for viewers.

- PEG parser grammar https://github.com/WordPress/gutenberg/tree/trunk/packages/block-serialization-spec-parser
- Default linear parser PHP/JS https://github.com/WordPress/gutenberg/tree/trunk/packages/block-serialization-spec-parser

## What is Scittle
- SCI https://github.com/babashka/SCI is lightweight Clojure(Script) interpreter.
- Scittle https://github.com/babashka/scittle/ executes Clojure(Script) directly from browser script tags via SCI.

SCI runs when page is loaded with Scittle, translating CLJS into Javascript on the fly.
It has performance penalty which can be avoided by transpiling JS ahead-of-time (with Squint).

## What is Reagent?
Reagent is popular and stable ReactJS wrapper https://github.com/reagent-project/reagent. There are a couple newer alternatives such as https://github.com/pitch-io/uix and https://github.com/cjohansen/replicant which might be interesting also but they didn't have Squint examples yet available.

## Ahead-of-time transpilation with Squint (TODO)
Squint https://github.com/squint-cljs/squint can transpile CLJS so client can be served simply Javascript.
