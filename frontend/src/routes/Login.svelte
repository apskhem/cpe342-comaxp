<script lang="ts">
  console.log("rendered");

  export let location: string;

  let isRequesting = false;

  const handleSubmit = (e: { currentTarget: HTMLFormElement }) => {
    if (isRequesting) {
      return;
    }

    const formData = new FormData(e.currentTarget);

    isRequesting = true;
  };
</script>

<template>
  <main>
    <div class="login-container">
      <form action="" class="form-container" on:submit|preventDefault={handleSubmit}>
        <div>Home</div>
        <h1>Login</h1>
        <div class="input-label-grid">
          <div class="input-label">
            <label for="login-username">Username</label>
            <input id="login-username" name="employeeNumber" type="text" disabled={isRequesting} required />
          </div>
          <div class="input-label">
            <label for="login-password">Username</label>
            <input id="login-password" name="password" type="password" disabled={isRequesting} required />
          </div>
        </div>
        <button type="submit" disabled={isRequesting}>Log In</button>
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
    min-height: calc(100vh - 54px - 28px);
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
</style>
