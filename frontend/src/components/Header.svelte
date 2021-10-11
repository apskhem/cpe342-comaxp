<script lang="ts">
  import { navigate } from "svelte-routing";
  import cx from "classnames";
  import { loginToken } from "../stores";

  let token = "";
  
  loginToken.subscribe((value) => token = value);

  const navigateOnce = (to: string) => {
    return () => {
      window.location.pathname != to && navigate(to);
    }
  };

  const logout = () => {
    loginToken.set("");
    localStorage.clear();
  }
</script>

<template>
  <header>
    <section class="upper-section">
      <aside>

      </aside>
      <aside class="logo-layout">
        <div on:click={navigateOnce("/")} class="logo-container" style="background-image: url(images/logo.png)"></div>
      </aside>
      <aside class="right-container">
        {#if token}
          <div on:click={() => {
            logout();
            navigate("/", { replace: true });
          }}>SIGN OUT</div>
        {:else}
          <div on:click={navigateOnce("/login")}>LOGIN</div>
        {/if}
      </aside>
    </section>
    <section class="lower-section">
      <div class="section-container">
        <div class="searchbar-row">
          <aside>
            <select name="" id="">
              <option value="" selected disabled>Vendor</option>
            </select>
          </aside>
          <aside>
            <div class="searchbar-container">
              <input type="text" placeholder="Search something here...">
              <div class="search-icon">
                <i class="fas fa-search"></i>
              </div>
            </div>
          </aside>
          <aside>
            <div class="cart-icon-container">
              <i class="fas fa-shopping-basket"></i>
            </div>
          </aside>
        </div>
        <div class={cx("controls-row", { "show": token })}>
          <aside on:click={navigateOnce("/management")}>
            Management
          </aside>
          <aside on:click={navigateOnce("/employees")}>
            Employees
          </aside>
          <aside on:click={navigateOnce("/customers")}>
            Customers
          </aside>
          <aside on:click={navigateOnce("/products")}>
            Products
          </aside>
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
    padding-top: 1em;

    .section-container {
      max-width: 800px;
      margin: 0 auto;

      .searchbar-row {
        display: grid;
        grid-template-columns: 144px 1fr 64px;
        gap: 1em;

        select {
          display: block;
          width: 100%;
          height: 100%;
          background-color: transparent;
          border: 0;
          margin: 0;
          cursor: pointer;

          &:focus {
            outline: 0;
          }
        }

        .cart-icon-container {
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;

          i {
            color: #A7A7A7;
            font-size: x-large;
          }
        }

        .searchbar-container {
          display: grid;
          grid-template-columns: 1fr 54px;
          border: 2px solid #A7A7A7;
          border-radius: 8px;
          overflow: hidden;

          input {
            padding: 6px 1em;
            border: 0;
          }

          .search-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #A7A7A7;
            background-color: #C4C4C4;
            font-size: large;
            
            i {
              cursor: pointer;
            }
          }
        }
      }

      .controls-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2px;
        margin-top: 1em;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
        opacity: 0;

        &.show {
          opacity: 1;
        }

        > aside {
          display: flex;
          align-items: center;
          justify-content: center;
          height: 48px;
          color: white;
          background-color: #C4C4C4;
          font-weight: bold;
          cursor: pointer;
        }
      }
    }
  }

  .fa-shopping-basket {
    cursor: pointer;
  }
</style>