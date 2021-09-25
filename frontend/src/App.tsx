import React, { Component } from "react";
import logo from "./logo.svg";
import "./App.scss";

class App extends Component {

  public override state = {

  };

  public constructor(props = {}) {
    super(props);
  }

  public override render(): JSX.Element {
    return (
      <div className="App">
        <header className="App-header">
          <img src={logo} className="App-logo" alt="logo" />
          <p>
            Edit <code>src/App.js</code> and save to reload.
          </p>
          <a
            className="App-link"
            href="https://reactjs.org"
            target="_blank"
            rel="noopener noreferrer"
          >
            Learn React
          </a>
        </header>
      </div>
    );
  }
}

export default App;
