// this file contains everything that needs to be required
// so rollup can be happy.

var jquery = require("jquery");

window.$ = jquery;
window.jQuery = jquery;


window.moment = require("moment");
window.toastr = require("toastr");

window.bootbox = require("bootbox");

// we need to fix these to use the global jquery we included.
var dt = require('datatables.net')(window, jquery);
var dts = require('datatables.net-select')(window, jquery);
var dtsb4 = require('datatables.net-select-bs4')(window, jquery);
var dtb4 = require('datatables.net-bs4')(window, jquery);
var colreorder = require('datatables.net-colreorder')(window, jquery);
var colreorderbs4 = require('datatables.net-colreorder-bs4')(window, jquery);
var rowgroup = require('datatables.net-rowgroup')(window, jquery);
var rowgroupbs4 = require('datatables.net-rowgroup-bs4')(window, jquery);