import "~fortawesome/sprites/duotone.svg";

import.meta.glob(["../images/**"]);

import { Application } from "@hotwired/stimulus";
import { registerControllers } from "stimulus-vite-helpers";
const application = Application.start();
const controllers = import.meta.globEager("./controllers/*_controller.js");
registerControllers(application, controllers);

import * as Turbo from "@hotwired/turbo";
Turbo.start();
