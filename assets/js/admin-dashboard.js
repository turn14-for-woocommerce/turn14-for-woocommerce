jQuery(document).ready(function($) {
    
    console.log('hey');

    $("#import-products-btn").on("click", function(e) {
        console.log("importing products");
        e.preventDefault();
        $.ajax({
          type: "POST",
          url: admin_dashboard.ajax,
          data:{action:'import_all_products'},
          success: function(data) {
            console.log("entry added successfully");
          }
        });
    });
});