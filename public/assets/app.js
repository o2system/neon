/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/app.js":
/*!**************************!*\
  !*** ./resources/app.js ***!
  \**************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _app_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./app.scss */ \"./resources/app.scss\");\n/* harmony import */ var _app_scss__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_app_scss__WEBPACK_IMPORTED_MODULE_1__);\n/**\r\n * This file is part of the O2System Framework package.\r\n *\r\n * For the full copyright and license information, please view the LICENSE\r\n * file that was distributed with this source code.\r\n *\r\n * @author         Steeve Andrian Salim\r\n * @copyright      Copyright (c) Steeve Andrian Salim\r\n */\n// ------------------------------------------------------------------------\n\n\nif ('serviceWorker' in navigator) {\n  navigator.serviceWorker.register('/service-worker.js').then(function (registration) {\n    console.info('Service Worker registration successful with scope: ', registration.scope);\n  })[\"catch\"](function (err) {\n    console.error('Service Worker registration failed: ', err);\n  });\n} else {\n  console.error('Service Worker registration failed, insecure page, please serve your page over HTTPS or use localhost');\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvYXBwLmpzPzhkZmIiXSwibmFtZXMiOlsibmF2aWdhdG9yIiwic2VydmljZVdvcmtlciIsInJlZ2lzdGVyIiwidGhlbiIsInJlZ2lzdHJhdGlvbiIsImNvbnNvbGUiLCJpbmZvIiwic2NvcGUiLCJlcnIiLCJlcnJvciJdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FBQUE7Ozs7Ozs7OztBQVNBO0FBRUE7O0FBRUEsSUFBSSxtQkFBbUJBLFNBQXZCLEVBQWtDO0FBQzlCQSxXQUFTLENBQUNDLGFBQVYsQ0FDS0MsUUFETCxDQUNjLG9CQURkLEVBRUtDLElBRkwsQ0FFVSxVQUFTQyxZQUFULEVBQXVCO0FBQ3pCQyxXQUFPLENBQUNDLElBQVIsQ0FDSSxxREFESixFQUVJRixZQUFZLENBQUNHLEtBRmpCO0FBSUgsR0FQTCxXQVFXLFVBQVNDLEdBQVQsRUFBYztBQUNqQkgsV0FBTyxDQUFDSSxLQUFSLENBQWMsc0NBQWQsRUFBc0RELEdBQXREO0FBQ0gsR0FWTDtBQVdILENBWkQsTUFZTztBQUNISCxTQUFPLENBQUNJLEtBQVIsQ0FBYyx1R0FBZDtBQUNIIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2FwcC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxyXG4gKiBUaGlzIGZpbGUgaXMgcGFydCBvZiB0aGUgTzJTeXN0ZW0gRnJhbWV3b3JrIHBhY2thZ2UuXHJcbiAqXHJcbiAqIEZvciB0aGUgZnVsbCBjb3B5cmlnaHQgYW5kIGxpY2Vuc2UgaW5mb3JtYXRpb24sIHBsZWFzZSB2aWV3IHRoZSBMSUNFTlNFXHJcbiAqIGZpbGUgdGhhdCB3YXMgZGlzdHJpYnV0ZWQgd2l0aCB0aGlzIHNvdXJjZSBjb2RlLlxyXG4gKlxyXG4gKiBAYXV0aG9yICAgICAgICAgU3RlZXZlIEFuZHJpYW4gU2FsaW1cclxuICogQGNvcHlyaWdodCAgICAgIENvcHlyaWdodCAoYykgU3RlZXZlIEFuZHJpYW4gU2FsaW1cclxuICovXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuaW1wb3J0IFwiLi9hcHAuc2Nzc1wiO1xyXG5cclxuaWYgKCdzZXJ2aWNlV29ya2VyJyBpbiBuYXZpZ2F0b3IpIHtcclxuICAgIG5hdmlnYXRvci5zZXJ2aWNlV29ya2VyXHJcbiAgICAgICAgLnJlZ2lzdGVyKCcvc2VydmljZS13b3JrZXIuanMnKVxyXG4gICAgICAgIC50aGVuKGZ1bmN0aW9uKHJlZ2lzdHJhdGlvbikge1xyXG4gICAgICAgICAgICBjb25zb2xlLmluZm8oXHJcbiAgICAgICAgICAgICAgICAnU2VydmljZSBXb3JrZXIgcmVnaXN0cmF0aW9uIHN1Y2Nlc3NmdWwgd2l0aCBzY29wZTogJyxcclxuICAgICAgICAgICAgICAgIHJlZ2lzdHJhdGlvbi5zY29wZVxyXG4gICAgICAgICAgICApO1xyXG4gICAgICAgIH0pXHJcbiAgICAgICAgLmNhdGNoKGZ1bmN0aW9uKGVycikge1xyXG4gICAgICAgICAgICBjb25zb2xlLmVycm9yKCdTZXJ2aWNlIFdvcmtlciByZWdpc3RyYXRpb24gZmFpbGVkOiAnLCBlcnIpO1xyXG4gICAgICAgIH0pO1xyXG59IGVsc2Uge1xyXG4gICAgY29uc29sZS5lcnJvcignU2VydmljZSBXb3JrZXIgcmVnaXN0cmF0aW9uIGZhaWxlZCwgaW5zZWN1cmUgcGFnZSwgcGxlYXNlIHNlcnZlIHlvdXIgcGFnZSBvdmVyIEhUVFBTIG9yIHVzZSBsb2NhbGhvc3QnKTtcclxufSJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/app.js\n");

/***/ }),

/***/ "./resources/app.scss":
/*!****************************!*\
  !*** ./resources/app.scss ***!
  \****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvYXBwLnNjc3M/MmE0NSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3Jlc291cmNlcy9hcHAuc2Nzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/app.scss\n");

/***/ }),

/***/ "./resources/themes/nitro/theme.scss":
/*!*******************************************!*\
  !*** ./resources/themes/nitro/theme.scss ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvdGhlbWVzL25pdHJvL3RoZW1lLnNjc3M/ZmJiYSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3Jlc291cmNlcy90aGVtZXMvbml0cm8vdGhlbWUuc2Nzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/themes/nitro/theme.scss\n");

/***/ }),

/***/ 0:
/*!*****************************************************************************************!*\
  !*** multi ./resources/app.js ./resources/app.scss ./resources/themes/nitro/theme.scss ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! D:\laragon\www\neon\resources\app.js */"./resources/app.js");
__webpack_require__(/*! D:\laragon\www\neon\resources\app.scss */"./resources/app.scss");
module.exports = __webpack_require__(/*! D:\laragon\www\neon\resources\themes\nitro\theme.scss */"./resources/themes/nitro/theme.scss");


/***/ })

/******/ });