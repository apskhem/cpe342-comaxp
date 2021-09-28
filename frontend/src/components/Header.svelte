<script lang="ts">
  import { Router, Link, navigate } from "svelte-routing";
</script>

<template>
  <header>
    <section class="upper-section">
      <aside>

      </aside>
      <aside class="logo-layout">
        <div on:click={() => navigate("/")} class="logo-container" style="background-image: url(images/logo.png)"></div>
      </aside>
      <aside class="right-container">
        <div on:click={() => navigate("/login")}>LOGIN</div>
      </aside>
    </section>
    <section class="lower-section">
      <div class="section-container">
        <div class="searchbar-row">
          <aside>
            <select name="" id="">
              <option value="" selected disabled>Catalog</option>
            </select>
          </aside>
          <aside>
            <div class="searchbar-container">
              <input type="text" placeholder="Search something here...">
              <div class="search-icon"></div>
            </div>
          </aside>
          <aside>
            
          </aside>
        </div>
        <div>

        </div>
      </div>
    </section>
  </header>
</template>

<style lang="scss">
  .upper-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-auto-rows: 100%;
    background-color: #A8A5A5;
    height: 54px;
    padding: 0.5em 2em;

    > aside {
      display: flex;
      align-self: center;
    }

    .logo-layout {
      height: 100%;
      width: 100%;
    }

    .logo-container {
      height: 100%;
      width: 35%;
      margin: 0 auto;
      background-position: center;
      background-repeat: no-repeat;
      background-size: contain;
      cursor: pointer;
    }

    .right-container {
      justify-self: end;

      div {
        cursor: pointer;
        text-decoration: underline;
      }
    }
  }

  .lower-section {
    background-color: #F3F3F3;
    padding: 1em 0;

    .section-container {
      max-width: 800px;
      margin: 0 auto;

      .searchbar-row {
        display: grid;
        grid-template-columns: 100px 1fr 100px;
        gap: 1em;

        select {
          display: block;
          width: 100%;
          height: 100%;
          background-color: transparent;
          border: 0;
          margin: 0;

          &:focus {
            outline: 0;
          }
        }

        .searchbar-container {
          display: grid;
          grid-template-columns: 1fr 80px;
          border: 2px solid #A7A7A7;
          border-radius: 8px;
          overflow: hidden;

          input {
            padding: 6px 1em;
            border: 0;
          }

          .search-icon {
            background-color: #C4C4C4;
          }
        }
      }
    }
  }
</style>