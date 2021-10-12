<script lang="ts">
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  let token = "";

  loginToken.subscribe((value) => token = value);

  let prof: Model.ICustomer;

  const start = async () => {
    const res = await fetch(`https://comaxp.herokuapp.com/api/customers/${id}`, {
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
    
</template>


<style lang="scss">
    
</style>