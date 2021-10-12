<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import { loginToken } from "../stores";

  export let location: string;

  let token = "";
  let list: null | Model.IPayment[] = null;

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch("https://comaxp.herokuapp.com/api/payments", {
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
              </tr>
            </thead>
            <tbody>
              {#each list.slice(0, 200) as el, i}
                <tr on:click={() => navigate(`/orders/${el.orderNumber}`)}>
                  <td>{i + 1}</td>
                  <td>{"test"}
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
</style>
