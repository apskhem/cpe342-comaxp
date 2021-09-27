<script lang="ts">
  import { Router, Link, Route } from "svelte-routing";
  import Home from "./routes/Home.svelte";
  import Header from "./components/Header.svelte";

  export let name: string;
</script>

<template>
  <Header />
  <Router>
    <Route path="/" component="{Home}" />
  </Router>
</template>

<style lang="scss" global>
  @import url(https://fonts.googleapis.com/css2?family=Roboto&display=swap);

  body {
    margin: 0;
    padding: 0;
    font-family:
      "Roboto",
      -apple-system,
      BlinkMacSystemFont,
      "Segoe UI",
      "Roboto",
      "Oxygen",
      "Ubuntu",
      "Cantarell",
      "Fira Sans",
      "Droid Sans",
      "Helvetica Neue",
      sans-serif;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  code {
    font-family: "source-code-pro", "Menlo", "Monaco", "Consolas", "Courier New", monospace;
  }
  
  html {
    @extend body;

    box-sizing: border-box;
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  button {
    display: block;
    width: 100%;
    border: none;
    font: inherit;
    cursor: pointer;
  }

  input {
    margin: 0;
    background-color: transparent;
    font: inherit;
    
    &:focus { outline: none; }
    &::-webkit-inner-spin-button { -webkit-appearance: none; }
  }

  a {
    text-decoration: none;

    &:visited {
      color: inherit;
    }
  }
  
  * {
    box-sizing: inherit;

    &:not(textarea),
    &:not([contenteditable=true]),
    &:not(input) {
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      user-select: none; /* supported by Chrome and Opera */
    }

    &::before,
    &::after {
      box-sizing: inherit;
    }
  }
</style>