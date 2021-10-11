<script lang="ts">
  import { Router, Link, Route } from "svelte-routing";
  import { loginToken } from "./stores";
  import Header from "./components/Header.svelte";
  import Footer from "./components/Footer.svelte";
  import Home from "./routes/Home.svelte";
  import Login from "./routes/Login.svelte";
  import EmployeeList from "./routes/EmployeeList.svelte";
  import CustomerList from "./routes/CustomerList.svelte";
  import ProductList from "./routes/ProductList.svelte";
  import NotFound from "./routes/NotFound.svelte";
  import Checkout from "./routes/Checkout.svelte";
  import PlaceOrder from "./routes/PlaceOrder.svelte";
  import CatalogView from "./routes/CatalogView.svelte";
  import EmployeeView from "./routes/EmployeeView.svelte";
  import CustomerView from "./routes/CustomerView.svelte";
  import ProductView from "./routes/ProductView.svelte";

  const start = () => {
    const loginItem = localStorage.getItem("token") ?? "";
    loginToken.set(loginItem);
  };

  start();
</script>

<template>
  <Header />
  <Router>
    <Route path="/" component="{Home}" />
    <Route path="/catalog" component="{Home}" />
    <Route path="/catalog/:id" component="{CatalogView}" />
    <Route path="/checkout" component="{Checkout}" />
    <Route path="/placeorder" component="{PlaceOrder}" />
    <Route path="/login" component="{Login}" />
    <Route path="/employees" component="{EmployeeList}" />
    <Route path="/employees/:id" component="{EmployeeView}" />
    <Route path="/customers" component="{CustomerList}" />
    <Route path="/customers/:id" component="{CustomerView}" />
    <Route path="/products" component="{ProductList}" />
    <Route path="/products/:id" component="{ProductView}" />
    <Route path="*" component="{NotFound}" />
  </Router>
  <Footer />
</template>

<style lang="scss" global>
  @import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");
  
  main {
    position: relative;
    min-height: calc(100vh - 174px - 64px);
  }

  body {
    margin: 0;
    padding: 0;
    font-family:
      "Roboto",
      -apple-system,
      BlinkMacSystemFont,
      "Segoe UI",
      "Roboto",
      "Oxygen",
      "Ubuntu",
      "Cantarell",
      "Fira Sans",
      "Droid Sans",
      "Helvetica Neue",
      sans-serif;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  code {
    font-family: "source-code-pro", "Menlo", "Monaco", "Consolas", "Courier New", monospace;
  }
  
  html {
    @extend body;

    box-sizing: border-box;
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  button {
    display: block;
    width: 100%;
    border: none;
    font: inherit;
    cursor: pointer;
  }

  input {
    margin: 0;
    background-color: transparent;
    font: inherit;
    
    &:focus { outline: none; }
    &::-webkit-inner-spin-button { -webkit-appearance: none; }
  }

  a {
    text-decoration: none;

    &:visited {
      color: inherit;
    }
  }
  
  * {
    box-sizing: inherit;

    &:not(textarea),
    &:not([contenteditable=true]),
    &:not(input) {
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* Internet Explorer/Edge */
      user-select: none; /* supported by Chrome and Opera */
    }

    &::before,
    &::after {
      box-sizing: inherit;
    }
  }
</style>