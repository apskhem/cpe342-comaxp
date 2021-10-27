<script lang="ts">
  import { navigate } from "svelte-routing";
  import { slide } from "svelte/types/runtime/transition";
  import FullWaiter from "../components/FullWaiter.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let location: string;

  let token = "";
  let list: Model.IPreOrder[];
  let isInAddingMode = false;
  let isAddingPending = false;
  let form: HTMLFormElement;
  let errMsg = "";

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/preorders`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });
    
    list = await res.json();

    console.log(list)
  };

  start();
</script>

<template>
  <main>
    {#if list}
      <div class="layout">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Order No.</th>
                <th>Upfront Price</th>
              </tr>
            </thead>
            <tbody>
              {#each list.slice(0, 200) as el, i}
                <tr on:click={() => navigate(`/orders/${el.orderNumber}`)}>
                  <td>{i + 1}</td>
                  <td>{el.orderNumber}</td>
                  <td>{el.upfrontPrice}
                    <div on:click|stopPropagation class="row-option">
                      <i class="fas fa-clipboard"></i>
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
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
  }

  .create-btn-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 1em;
  }

  .form-btn-container {
    display: flex;
    margin-top: 1em;
    justify-content: center;
  }

  .create-form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;

    input, select {
      width: 100%;
      padding: 6.4px 12px;

      &[required]:empty {
        border-color: darkorange;
      }
    }

    select {
      height: 38.78px;
      border-radius: 0;
      margin: 0;
      &:focus {
        outline: 0;
      }
    }

    .form-half-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
  }

  .table-container {
    margin-top: 1em;
  }

  .err-msg {
    color: #AB1A1A;
    font-size: small;
    text-align: center;
  }
</style>
