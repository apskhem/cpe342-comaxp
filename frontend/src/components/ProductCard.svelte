<script lang="ts">
  import { cartProduct } from "../stores";

  export let data: Model.IProduct;

  let cart: Map<string, [ number, Model.IProduct ]>;

  cartProduct.subscribe((value) => cart = value);

  const addToCart = () => {
    // if product type is exist
    if (cart.has(data.productCode)) {
      const arr = cart.get(data.productCode);

      if (!arr) {
        throw new Error("Assertion failed");
      }

      arr[0] += 1;
    }
    // otherwise
    else {
      cart.set(data.productCode, [ 1, data ]);
    }

    cartProduct.set(cart);
  }
</script>

<template>
  <div class="product-card">
    <div class="image-container">
      <div class="scale">{data.productScale}</div>
      <div class="add-to-cart-icon" on:click={addToCart}>
        <i class="fas fa-cart-plus"></i>
      </div>
      <div class="image-background"></div>
    </div>
    <div class="desc-container">
      <div class="upper-desc">
        <div class="product-name">{data.productName}</div>
        <div class="vendor-name">{data.productLine}</div>
      </div>
      <div class="lower-desc">
        <div class="product-price">
          <span class="current-price">${data.buyPrice}</span>
          <span class="killed-price">${data.MSRP}</span>
        </div>
        <div class="product-remaining-count">
          <span>{data.quantityInStock.toLocaleString("en")}</span>
          <i class="fas fa-boxes"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
  .product-card {
    display: grid;
    grid-template-rows: 186px 1fr;
    border: 1px solid #E1DEDE;

    > .image-container {
      position: relative;
      border-bottom: 1px solid #E1DEDE;
      padding: 0.5em;

      > .scale {
        position: absolute;
        z-index: 1;
        color: #717171;
      }

      > .add-to-cart-icon {
        position: absolute;
        right: 0.5em;
        z-index: 1;
        color: #717171;
        font-size: large;
        cursor: pointer;
      }

      .image-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: lightgray;

        background-image: url("../images/image-not-available.png");
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
      }
    }

    > .desc-container {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 0.5em;

      > .upper-desc {
        > .product-name {
          font-size: large;
          color: #717171;
        }

        > .vendor-name {
          font-size: small;
          color: #B4AFAF;
        }
      }

      > .lower-desc {
        display: flex;
        justify-content: space-between;

        > .product-price {
          > .current-price {
            color: #AB1A1A;
            font-weight: bold;
            margin-right: 0.5em;
          }

          > .killed-price {
            color: #D4D2D4;
            text-decoration: line-through;
          }
        }

        > .product-remaining-count {
          > i {
            margin-left: 4px;
          }
        }
      }
    }
  }
</style>
