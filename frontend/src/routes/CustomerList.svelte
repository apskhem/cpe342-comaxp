<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import { loginToken } from "../stores";

  export let location: string;

  let token = "";
  let list: null | Model.ICustomer[] = null;

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch("https://comaxp.herokuapp.com/api/customers", {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });
    list = await res.json();

    console.log(list);
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
                <th>Full Name</th>
                <th>Phone</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
              </tr>
            </thead>
            <tbody>
              {#each list as el, i}
                <tr on:click={() => navigate(`/customers/${el.customerNumber}`)}>
                  <td>{i + 1}</td>
                  <td>{`${el.contactLastName} ${el.contactLastName}`}</td>
                  <td>{el.phone}</td>
                  <td>{el.city}</td>
                  <td>{el.state || ""}</td>
                  <td>{el.country}
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
