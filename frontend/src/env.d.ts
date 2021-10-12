declare module "*.svelte" {
  export { SvelteComponentDev as default } from "svelte/internal";
}

module Response {
  interface Login {
    Token?: string;
    message?: string;
  }

  type GetEmployeeList = Model.IEmployee[];
  type GetCustomerList = Model.ICustomer[];
  type GetProductList = Model.IProduct[];
}

module Model {
  interface IEmployee {
    email: string;
    employeeNumber: number;
    extension: string;
    firstName: string;
    lastName: string;
    jobTitle: string;
    officeCode: string;
    reportsTo: null | number;
  }

  interface ICustomer {
    addressLine1: string;
    addressLine2: string | null;
    city: string;
    contactFirstName: string;
    contactLastName: string;
    country: string;
    creditLimit: string;
    customerName: string;
    customerNumber: number;
    memberPoint: number;
    phone: string;
    postalCode: string;
    salesRepEmployeeNumber: number;
    state: string | null;
  }

  interface IProduct {
    MSRP: string;
    buyPrice: string;
    productCode: string;
    productDescription: string;
    productLine: string;
    productName: string;
    productScale: string;
    productVendor: string;
    quantityInStock: number;
  }

  interface IOrder {
    comments: string | null;
    customerNumber: number;
    discountCode: string | null;
    orderDate: string;
    orderLineNumber: number;
    orderNumber: number;
    priceEach: string;
    productCode: string;
    quantityOrdered: number;
    requiredDate: string;
    shippedDate: string;
    status: string;
  }

  interface IPayment {
    orderNumber: number;
    customerNumber: number;
    checkNumber: string;
    paymentDate: string;
    amount: number;
  }
}

type Cart = Map<string, [ number, Model.IProduct ]>