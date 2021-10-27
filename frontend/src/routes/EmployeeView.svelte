<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  let token = "";
  let isEditing = false;
  let isDeletingPending = false;
  let isSubmitingPending = false;
  let payload: Model.IEmployee;
  let changeMap = new Map<keyof Model.IEmployee, string>();

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/employees/${id}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });
    
    payload = (await res.json())[0];

    console.log(payload);
  };

  start();

  const handleEdit = async () => {
    isEditing = !isEditing;

    // is in submitting mode
    if (isEditing || isSubmitingPending || !changeMap.size) {
      return;
    }

    isSubmitingPending = true;

    try {
      // transform map
      const formData1 = new URLSearchParams();

      changeMap.forEach((v, k) => {
        if (k !== "jobTitle") {
          formData1.append(k, v);
        }
      });

      // build request
      const fetch1 = fetch(`${FETCH_ROOT}/api/employees/${id}`, {
        method: "put",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        }),
        body: formData1
      });

      // process change map
      if (changeMap.has("jobTitle")) {
        // transform map
        const formData2 = new URLSearchParams();

        formData2.append("jobTitle", changeMap.get("jobTitle") as string);

        // send request
        const [ res1, res2 ] = await Promise.all([
          fetch1,
          fetch(`${FETCH_ROOT}/api/employees-management/${id}`, {
            method: "put",
            headers: new Headers({
              "Authorization": `Bearer ${token}`
            }),
            body: formData2
          })
        ]);

        // handle request
        if (res1.status === 200 && res2.status === 200) {
          console.log(res1.status, res2.status);
        }
        else {
          throw new Error("the status is not 200");
        }
      }
      else {
        const res = await fetch1;

        // handle request
        if (res.status === 200) {
          console.log(res.status);
        }
        else {
          throw new Error("the status is not 200");
        }
      }

      // handle error
      changeMap.clear();
    }
    catch (err) {
      console.error(err);
    }
    finally {
      isSubmitingPending = false;
    }
  };

  const handleDelete = async () => {
    if (isDeletingPending) {
      return;
    }

    isDeletingPending = true;

    try {
      // send request
      const res = await fetch(`${FETCH_ROOT}/api/employees/${id}`, {
        method: "delete",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        })
      });

      // handle error
      if (res.status === 200) {
        navigate(`/employees`, { replace: true });
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

  const handleInputChange = <T extends keyof Model.IEmployee>(channel: T) => {
    return (e: { currentTarget: EventTarget & HTMLInputElement | HTMLSelectElement }) => {
      changeMap.set(channel, e.currentTarget.value);
    };
  };
</script>

<template>
  <main>
    {#if payload}
      <div class="layout">
        <div class="two-grid">
          <aside>
            <div class="profile-img">
              <img src="../images/person-placeholder.png" alt="person" />
            </div>
          </aside>
          <aside class="info-grid">
            <section class="info-header">
              <div class="id">#{payload.employeeNumber}</div>
              <div>
                <span class="name">{payload.firstName} {payload.lastName}</span>
              </div>
            </section>
            <section class="form-information">
              <aside>E-mail:</aside>
              <input
                type="text"
                value={payload.email}
                disabled
                required
                on:change={handleInputChange("email")}
              >
              <aside>Extension:</aside>
              <input
                type="text"
                value={payload.extension}
                disabled={!isEditing}
                required
                on:change={handleInputChange("extension")}
              >
              <aside>Office Code:</aside>
              <input
                type="text"
                value={payload.officeCode}
                disabled={!isEditing}
                required
                on:change={handleInputChange("officeCode")}
              >
              <aside>Supervisor:</aside>
              <input
                type="text"
                value={payload.reportsTo ?? ""}
                placeholder="none"
                disabled={!isEditing}
                required
                on:change={handleInputChange("reportsTo")}
              >
              <aside>Position:</aside>
              <select
                value={payload.jobTitle}
                disabled={!isEditing}
                required
                on:change={handleInputChange("jobTitle")}
              >
                <optgroup label="Representative">
                  <option value="Sales Rep">Sales Rep</option>
                </optgroup>
                <optgroup label="President">
                  <option value="President">President</option>
                </optgroup>
                <optgroup label="Vice President">
                  <option value="VP Marketing">VP Marketing</option>
                  <option value="VP Sales">VP Sales</option>
                </optgroup>
                <optgroup label="Sales Manager">
                  <option value="Sales Manager (EMEA)">Sales Manager (EMEA)</option>
                  <option value="Sales Manager (APAC)">Sales Manager (APAC)</option>
                </optgroup>
              </select>
            </section>
            <section class="danger-zone">
              <div class="danger-label">Danger Zone</div>
              <MajorButton
                width="200px"
                label={isEditing ? "Submit" : "Edit"}
                isPending={isSubmitingPending}
                on:click={handleEdit}
              />
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