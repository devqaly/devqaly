<template>
  <pre
    class="bg-black-alpha-90 text-white p-4 border-round-md"
    style="tab-size: 6; white-space-collapse: preserve-breaks"
  >
    <slot></slot>
  </pre>
</template>

<style>
code,
pre {
  color: black;
  background: none;
  font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
  text-align: left;
  white-space: pre;
  word-spacing: normal;
  word-break: normal;
  word-wrap: normal;
  line-height: 1.5;

  -moz-tab-size: 4;
  -o-tab-size: 4;
  tab-size: 4;

  -webkit-hyphens: none;
  -moz-hyphens: none;
  -ms-hyphens: none;
  hyphens: none;
}

/* Code blocks */
pre {
  position: relative;
  margin: 0.5em 0;
  box-shadow: -1px 0px 0px 0px #358ccb, 0px 0px 0px 1px #dfdfdf;
  border-left: 10px solid #358ccb;
  background-color: #fdfdfd;
  background-image: linear-gradient(transparent 50%, rgba(69, 142, 209, 0.04) 50%);
  background-size: 3em 3em;
  background-origin: content-box;
  overflow: visible;
  padding: 0;
}

code[class*='language'] {
  max-height: inherit;
  height: 100%;
  padding: 0 1em;
  display: block;
  overflow: auto;
}
</style>
