/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 121);
/******/ })
/************************************************************************/
/******/ ({

/***/ 115:
/***/ (function(module, exports) {

throw new Error("Module build failed: Error: ENOENT: no such file or directory, open '/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/node_modules/babel-polyfill/lib/index.js'\n    at Error (native)");

/***/ }),

/***/ 116:
/***/ (function(module, exports) {

throw new Error("Module build failed: Error: ENOENT: no such file or directory, open '/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/node_modules/uikit/dist/js/uikit-icons.js'\n    at Error (native)");

/***/ }),

/***/ 117:
/***/ (function(module, exports) {

throw new Error("Module build failed: Error: ENOENT: no such file or directory, open '/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/node_modules/uikit/dist/js/uikit.js'\n    at Error (native)");

/***/ }),

/***/ 118:
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(313)

var Component = __webpack_require__(311)(
  /* script */
  __webpack_require__(120),
  /* template */
  __webpack_require__(312),
  /* scopeId */
  "data-v-aeae57de",
  /* cssModules */
  null
)
Component.options.__file = "/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/src/App.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] App.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-aeae57de", Component.options)
  } else {
    hotAPI.reload("data-v-aeae57de", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),

/***/ 119:
/***/ (function(module, exports) {

throw new Error("Module build failed: Error: ENOENT: no such file or directory, open '/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/node_modules/vue/dist/vue.runtime.esm.js'\n    at Error (native)");

/***/ }),

/***/ 120:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
	data() {
		return {
			msg: 'Hello world!'
		};
	}
});

/***/ }),

/***/ 121:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_polyfill__ = __webpack_require__(115);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_polyfill___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_polyfill__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_uikit__ = __webpack_require__(117);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_uikit___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_uikit__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_uikit_dist_js_uikit_icons__ = __webpack_require__(116);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_uikit_dist_js_uikit_icons___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_uikit_dist_js_uikit_icons__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vue__ = __webpack_require__(119);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__App_vue__ = __webpack_require__(118);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__App_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4__App_vue__);


// ------------------------------------------------------------------------------------------------ Uikit components ---



// loads the Icon plugin
__WEBPACK_IMPORTED_MODULE_1_uikit__["default"].use(__WEBPACK_IMPORTED_MODULE_2_uikit_dist_js_uikit_icons__["default"]);



// -------------------------------------------------------------------------------------------------- App components ---


window.onload = function () {

	new __WEBPACK_IMPORTED_MODULE_3_vue__["default"]({
		el: '#app',
		render: h => h(__WEBPACK_IMPORTED_MODULE_4__App_vue___default.a)
	});
};

/***/ }),

/***/ 305:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(306)(undefined);
// imports


// module
exports.push([module.i, "\n.example[data-v-aeae57de] {\n  color: red;\n}\n", ""]);

// exports


/***/ }),

/***/ 306:
/***/ (function(module, exports) {

throw new Error("Module build failed: Error: ENOENT: no such file or directory, open '/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/node_modules/css-loader/lib/css-base.js'\n    at Error (native)");

/***/ }),

/***/ 311:
/***/ (function(module, exports) {

throw new Error("Module build failed: Error: ENOENT: no such file or directory, open '/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/node_modules/vue-loader/lib/component-normalizer.js'\n    at Error (native)");

/***/ }),

/***/ 312:
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "example"
  }, [_vm._v(_vm._s(_vm.msg))])
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-aeae57de", module.exports)
  }
}

/***/ }),

/***/ 313:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(305);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(314)("3f678546", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../node_modules/css-loader/index.js!../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-aeae57de\",\"scoped\":true,\"hasInlineConfig\":false}!../node_modules/less-loader/dist/index.js!../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./App.vue", function() {
     var newContent = require("!!../node_modules/css-loader/index.js!../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-aeae57de\",\"scoped\":true,\"hasInlineConfig\":false}!../node_modules/less-loader/dist/index.js!../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./App.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 314:
/***/ (function(module, exports) {

throw new Error("Module build failed: Error: ENOENT: no such file or directory, open '/home/felix/Dropbox/WebProjekte/owc.007.07_core/apps/market/node_modules/vue-style-loader/lib/addStylesClient.js'\n    at Error (native)");

/***/ })

/******/ });