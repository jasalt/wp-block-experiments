import { PhpWeb } from '/wp-content/plugins/wp-block-experiments/node_modules/php-wasm/PhpWeb.mjs';

window.PhpWeb = PhpWeb;

console.log("wasmInitializer.mjs set window.PhpWeb");
