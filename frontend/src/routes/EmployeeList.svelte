<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";

  export let location: string;

  let list: null | Model.IEmployee[] = null;
  let employeeMap = new Map<number, string>();

  const start = async () => {
    const res = await fetch("https://comaxp.herokuapp.com/api/employees");
    const data: Response.GetEmployeeList = await res.json();

    list = data;

    console.log(data);

    for (const employee of data) {
      employeeMap.set(employee.employeeNumber, employee.firstName + " " + employee.lastName);
    }
  };

  start();
</script>

<template>
  <main>
    {#if list && employeeMap.size}
      <div class="layout table-container">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Employee No.</th>
              <th>Full Name</th>
              <th>Extension</th>
              <th>Email</th>
              <th>Reports To</th>
              <th>Job Title</th>
            </tr>
          </thead>
          <tbody>
            {#each list as employee, i}
              <tr on:click={() => navigate(`/employee/${employee.employeeNumber}`)}>
                <td>{i + 1}</td>
                <td>{employee.employeeNumber}</td>
                <td>{employeeMap.get(employee.employeeNumber)}</td>
                <td>{employee.extension}</td>
                <td>{employee.email}</td>
                <td>{employeeMap.get(employee.reportsTo ?? NaN) ?? ""}</td>
                <td>{employee.jobTitle}
                  <div class="row-option">
                    <i on:click|stopPropagation class="fas fa-clipboard"></i>
                  </div>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
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

  .table-container {
    margin-top: 2em;
    border: 1px solid #E1DEDE;
    overflow-x: auto;
  }

  table {
    position: relative;
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    z-index: 0;

    > thead {

      > tr {
        > th {
          position: sticky;
          position: -webkit-sticky;
          top: 0;
          padding: 8px;
          border: 1px solid #E1DEDE;
          text-align: left;

          > i {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translate(0, -50%);
            cursor: pointer;
          }
        }

        /* no. */
        &:first-child {
          > th:first-child {
            text-align: center;
            width: 40px;
          }
        }
      }
    }

    > tbody {
      > tr {
        transition: background-color 0.3s;

        &:hover {
          background-color: whitesmoke;

          > td:last-child {
            > div {
              opacity: 1;
              transform: translate(-100%, -50%);
            }
          }
        }

        &.deleting {
          background-color: #f6dfeb;
        }

        > td {
          padding: 4px 8px;
          white-space: nowrap;
          border: 1px solid #E1DEDE;
          overflow: hidden;
          text-overflow: ellipsis;

          &:first-child {
            text-align: center;
            font-weight: bold;
          }

          &:last-child {
            position: relative;

            > div {
              display: flex;
              align-items: center;
              gap: 0.5em;
              top: 50%;
              right: 0;
              position: absolute;
              height: 100%;
              transform: translate(0, -50%);
              transition: 300ms;
              opacity: 0;

              > i {
                cursor: pointer;
              }
            }
          }

          &:empty:after {
            content: "N/A";
            color: white;
            font-weight: bold;
            background-color: #AB1A1A;
            padding: 0 4px;
            border-radius: 2px;
          }

          &.editor {
            text-align: center;

            > i {
              cursor: pointer;

              &:not(:first-child) {
                margin-left: 8px;
              }

              &:first-child {
                color: #5d534a;
              }

              &:last-child {
                color: #cd5d7d;
              }
            }
          }

          &.pending {
            width: 68px;
            height: 28px;
          }

          ol {
            margin: 0;
            padding-left: 2em;

            li {
              list-style-type: circle !important;
            }
          }
        }

        &.disabled {
          background-color: lightgrey;

          i {
            color: #5d534a !important;
          }
        }
      }
    }
  }
</style>
