/**
  * JS Script for admin panel
  * 
  * @author Sam Hall https://github.com/hallsamuel90
  */
jQuery(document).ready(function($) {
  const SUCCESS = "success";
  const INFO = "info";
  const ERROR = "error";
  
  /**
   *
   */
  $("#import-products-btn").on("click", function(e) {
    console.log("importing products");
    e.preventDefault();
    $("#import-product-spinner").addClass("is-active");
    $.ajax({
      type: "POST",
      url: admin_dashboard.ajax,
      data: { action: "import_all_products" },
      success: function(data) {
        msg = data.data.msg;
        console.log(msg);
        $("#import-product-spinner").removeClass("is-active");
        notice(msg, INFO);
      }
    });
  });

  /**
   *
   */
  $("#delete-products-btn").on("click", function(e) {
    console.log("deleting products");
    e.preventDefault();
    $("#delete-product-spinner").addClass("is-active");
    $.ajax({
      type: "POST",
      url: admin_dashboard.ajax,
      data: { action: "delete_all_products" },
      success: function(data) {
        msg = data.data.msg;
        console.log(msg);
        $("#delete-product-spinner").removeClass("is-active");
        notice(msg, INFO);
      }
    });
  });

  /**
   *
   * @param {*} msg
   */
  function notice(msg, type) {
    $("#notice").append(
      "<div class='notice notice-" +
        type +
        " is-dismissible '><p>" +
        msg +
        "</p>" +
        "<button type='button' class='notice-dismiss'>" +
        "<span class='screen-reader-text'>Dismiss this notice.</span>" +
        "</button></div>"
    );
    $(".notice-dismiss").on("click", function(e) {
      $(this)
        .parent()
        .remove();
    });
  }
});
