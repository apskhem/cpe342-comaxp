<script lang="ts">
  import { Router, Link } from "svelte-routing";
</script>

<template>
  <Router>
    <header>
      <aside>

      </aside>
      <aside id="aside-logo-container" style="background-image: url(images/logo.png)"></aside>
      <aside id="right-container">
        <Link to="/login">
          <div>LOGIN</div>
        </Link>
      </aside>
    </header>
  </Router>
</template>

<style lang="scss">
  header {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-auto-rows: 100%;
    background-color: #A8A5A5;
    height: 54px;
    padding: 0.5em 2em;

    > aside {
      display: flex;
      align-self: center;
      justify-self: center;
    }
  }

  #aside-logo-container {
    height: 100%;
    width: 100%;
    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
  }

  #right-container {
    justify-self: end;

    div {
      cursor: pointer;
      text-decoration: underline;
    }
  }
</style>