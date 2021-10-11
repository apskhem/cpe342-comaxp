<script lang="ts">
  import { navigate } from "svelte-routing";
  import cx from "classnames";
  import { loginToken } from "../stores";

  export let location: string;

  let isRequesting = false;
  let isError = false;

  const encodeForm = (formData: FormData) => {
    const entries: [string, string][] = [];

    formData.forEach((v, k) => entries.push([k, v as string]));

    const res = entries
      .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
      .join("&");

    return res;
  }

  const handleSubmit = async (e: { currentTarget: HTMLFormElement }) => {
    if (isRequesting) {
      return;
    }

    isError = false;
    isRequesting = true;

    try {
      const formData = new FormData(e.currentTarget);
      const encoded = encodeForm(formData);

      const res = await fetch(`https://comaxp.herokuapp.com/api/login?${encoded}`);
      const data: Response.Login = await res.json();

      if (data.Token) {
        localStorage.setItem("token", data.Token);
        loginToken.set(data.Token)
        navigate("/");
      }
      else {
        throw new Error(data.toString());
      }
    }
    catch (err) {
      isError = true;
      console.error(err);
    }
    finally {
      isRequesting = false;
    }
  };
</script>

<template>
  <main>
    <div class="login-container">
      <form action="" class="form-container" on:submit|preventDefault={handleSubmit}>
        <div class="back-btn" on:click={() => window.history.back()}>
          <i class="fas fa-chevron-left"></i>
          <span>Back</span>
        </div>
        <h1>Login</h1>
        <div class="input-label-grid">
          <div class="input-label">
            <label for="login-username">Username</label>
            <input id="login-username" name="employeeNumber" type="text" disabled={isRequesting} required />
          </div>
          <div class="input-label">
            <label for="login-password">Password</label>
            <input id="login-password" name="password" type="password" disabled={isRequesting} required />
          </div>
        </div>
        <div class="submit-btn-container">
          <button type="submit" disabled={isRequesting}>{ isRequesting ? "Logging In..." : "Log In" }</button>
          <div class={cx("err-msg", { "show": isError })}>Incorrect username or password.</div>
        </div>
      </form>
      <aside class="wallpaper-container">
        <img src="images/logo.png" alt="logo" width="240px">
        <img src="images/login-wall.png" alt="wallpaper" width="440px">
      </aside>
    </div>
  </main>
</template>

<style lang="scss">
  main {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-container {
    display: grid;
    grid-template-columns: 324px 1fr;
    width: 800px;
    height: 460px;
    background-color: white;
    box-shadow: 0 2px 0.5em rgba($color: #000000, $alpha: 0.15);
  }

  .wallpaper-container {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    background-color: #E9E9E9;
  }

  .form-container {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1em;
    color: #C1C1C1;
  }

  .back-btn {
    cursor: pointer;

    i {
      margin-right: 8px;
    }
  }

  h1 {
    text-align: center;
    margin: 0;
  }

  button {
    color: white;
    background-color: #B8B8B8;
    border-radius: 4px;
    transition: 300ms;

    &:disabled {
      opacity: 0.6;
      pointer-events: none;
    }
  }

  .input-label-grid {
    display: grid;
    gap: 1em;
  }

  .input-label {
    input {
      display: block;
      width: 100%;
      height: 38px;
      border-radius: 4px;
      transition: 300ms;
    }
  }

  .err-msg {
    font-size: small;
    color: red;
    text-align: center;
    opacity: 0;

    &.show {
      opacity: 1;
    }
  }
</style>
