<script lang="ts">
  
</script>

<template>
  <div class="product-card">
    <div class="image-container">
      <div class="scale">scale</div>
      <div class="image-background"></div>
    </div>
    <div class="desc-container">
      <div class="upper-desc">
        <div class="product-name">Product 1</div>
        <div class="vendor-name">Company</div>
      </div>
      <div class="lower-desc">
        <div class="product-price">
          <span class="current-price">$XXX</span>
          <span class="killed-price">$XXX</span>
        </div>
        <div class="product-remaining-count">
          <span>102</span>
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
        color: #C0BABA;
      }

      .image-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: lightcyan;
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
