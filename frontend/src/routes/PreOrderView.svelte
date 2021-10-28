<script lang="ts">
import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  type Item = Model.ICustomer;

  let token = "";
  let isEditing = false;
  let isDeletingPending = false;
  let isSubmitingPending = false;
  let payload: Item;
  let changeMap = new Map<keyof Item, string>();

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/preorders/${id}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    payload = (await res.json())[0];

    console.log(payload);
  };

  start();

  const handleDelete = async () => {
    if (isDeletingPending) {
      return;
    }

    isDeletingPending = true;

    try {
      // send request
      const res = await fetch(`${FETCH_ROOT}/api/preorders/${id}`, {
        method: "delete",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        })
      });

      // handle error
      if (res.status === 200) {
        navigate(`/preorders`, { replace: true });
      }
      else {
        throw new Error(res.statusText);
      }
    }
    catch (err) {
      console.error(err);
    }
    finally {
      isDeletingPending = false;
    }
  };
</script>

<template>
  <main>
    {#if payload}
      <div class="layout">
        <div class="two-grid">
          <aside>
            <div class="profile-img">
              <img src="../images/image-not-available.png" alt="person" />
            </div>
          </aside>
          <aside class="info-grid">
            <section class="info-header">
              <div class="id">#{id}</div>
            </section>
            <section class="danger-zone">
              <div class="danger-label">Danger Zone</div>
              <MajorButton
                width="200px"
                label={"Delete"}
                isPending={isDeletingPending}
                on:click={handleDelete}
              />
            </section>
          </aside>
        </div>
      </div>
    {:else}
      <FullWaiter />
    {/if}
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
    border-radius: 8px;
    overflow: hidden;

    > img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  .info-grid {
    display: grid;
    gap: 1em;
  }

  .info-header {
    .id {
      color: lightgray;
      font-size: smaller;
    }

    .name {
      font-weight: bold;
      font-size: xx-large;
    }
  }

  .form-information {
    display: grid;
    grid-template-columns: 148px 1fr;
    gap: 8px;
    align-items: center;

    > :nth-child(odd) {
      font-weight: bold;
    }
  }

  .danger-zone {
    display: grid;
    gap: 8px;

    .danger-label {
      color: #AB1A1A;
      font-weight: bold;
      border-bottom: 1px solid lightgrey;
    }
  }
</style>