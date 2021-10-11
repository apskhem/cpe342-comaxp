<script lang="ts">
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  let token = "";

  loginToken.subscribe((value) => token = value);

  let prof: Model.IEmployee;

  const start = async () => {
    const res = await fetch(`https://comaxp.herokuapp.com/api/employees/${id}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });
    prof = (await res.json())[0];

    console.log(prof);
  };

  start();

  console.log(id);
</script>

<template>
  <main>
    <div class="layout">
      <div class="two-grid">
        <aside>
          <div class="profile-img"></div>
        </aside>
        <aside>
          <div >#9999</div>
          <div>
            <span>full name</span>
            <span>location</span>
          </div>
          <div>position</div>
          <section class="contact-information">
            <div>
              <aside>Phone</aside>
              <aside>xxxxx</aside>
            </div>
            <div>
              <aside>Email</aside>
              <aside>xxxxx</aside>
            </div>
          </section>
          <section class="danger-zone">

          </section>
        </aside>
      </div>
    </div>
  </main>
</template>


<style lang="scss">
  .layout {
    max-width: 900px;
    margin: 0 auto;
    padding-top: 2em;
  }

  .two-grid {
    display: grid;
    grid-template-columns: 224px 1fr;
    gap: 2em;
  }

  .profile-img {
    height: 224px;
    background-color: #E1DEDE;
  }
</style>