(function ($) {
  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
  /**
   * Ajax Request for managing the export user into csv
   */

  $('.zpcode_all').on("select2:select", function (e) { 
    var data = e.params.data.text;
    if(data=='All'){
    //$(".zpcode_all > option").attr("aria-selected","true");
    $(".zpcode_all > option").prop("selected","selected");
    $(".zpcode_all").trigger("change");
    }
  });

  jQuery( function($) {       
    $('.requestedzipcodes .delete, .zbcp_page_zbcp-users-requests #doaction, .toplevel_page_zbcp #doaction, .zbcp_page_zbcp-requests #doaction, .toplevel_page_zbcp .row-actions .delete').click( function( event ) {
        if( ! confirm( 'Are you sure you want to proceed with this action?' ) ) {
            event.preventDefault();
        }           
    });
    $('.zbcp_page_zbcp-users-requests #bulk-action-selector-top option, .zbcp_page_zbcp-requests #bulk-action-selector-top option, .toplevel_page_zbcp #bulk-action-selector-top option').val('bulk-delete');
  });


  $(document).on("click", ".export-users", function (e) {
    var zip_code = $(this).data("zip");
    $(this).val("Exporting...");
    jQuery.ajax({
      type: "POST",
      url: frontend_ajax_object.ajaxurl,
      data: {
        action: "export_registered_users_in_zipcode",
        zip_code: zip_code,
      },
      success: function (data) {
        console.log(data);
        /*
         * Make CSV downloadable
         */
        var downloadLink = document.createElement("a");
        var fileData = ["\ufeff" + data];
        var blobObject = new Blob(fileData, {
          type: "text/csv;charset=utf-8;",
        });
        var url = URL.createObjectURL(blobObject);
        downloadLink.href = url;
        downloadLink.download = "zbcp-" + zip_code + ".csv";
        /*
         * Actually download CSV
         */
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        $(".export-users").val("Export to CSV");
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError);
        $(".export-users").val("Export to CSV");
      },
    });
    e.preventDefault();
  });
  /**
   * Ajax Request for managing the preview users in Model
   */
  $(document).on("click", ".preview-users", function (e) {
    var zip_code = $(this).data("zip");
    $(this).val("Loading...");
    jQuery.ajax({
      type: "POST",
      url: frontend_ajax_object.ajaxurl,
      data: {
        action: "preview_registered_users_in_zipcode",
        zip_code: zip_code,
      },
      success: function (data) {
        var res = JSON.parse(data);
        if (res.status == true) {
          $(".zbcp_modal-header h2").text("Users Email");
          $(".zbcp_modal-body").html(res.result);
          $("#myModal").show();
        } else {
          alert(res.result);
        }
        $(".preview-users").val("Preview");
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError);
        $(".preview-users").val("Preview");
      },
    });
    e.preventDefault();
  });
  $(document).on("click", ".zbcp_close", function () {
    $("#myModal").hide();
  });
  /**
   * Ajax Request for managing the view post type against a zipcode
   */
  $(document).on("click", ".view-post-types", function (e) {
    var zip_code = $(this).data("zip");
    $(this).val("Viewing...");
    jQuery.ajax({
      type: "POST",
      url: frontend_ajax_object.ajaxurl,
      data: {
        action: "view_posts_registered_users_in_zipcode",
        zip_code: zip_code,
      },
      success: function (data) {
        var res = JSON.parse(data);
        if (res.status == true) {
          $(".zbcp_modal-header h2").text("Post Types");
          $(".zbcp_modal-body").html(res.result);
          $("#myModal").show();
        } else {
          alert(res.result);
        }
        $(".view-post-types").val("View");
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError);
        $(".view-post-types").val("View");
      },
    });
    e.preventDefault();
  });
  /**
   * Ajax Request for insert a zipcode into database
   */
  $(document).on("click", "#zipcode_button", function (e) {
    var zipcodeVal = $("#zipcode_field").val();
    if (zipcodeVal != "") {
      $(".zbcp_loader").show();
      jQuery.ajax({
        type: "POST",
        url: frontend_ajax_object.ajaxurl,
        data: {
          action: "insert_zipcode_into_database",
          zipcode: zipcodeVal,
        },
        success: function (data) {
          var res = JSON.parse(data);
          if (res.status == true) {
            jQuery(".success-msg-zipcode").removeClass("display-success-msg");
            setTimeout(function() {
              jQuery('.success-msg-zipcode').fadeOut('slow');
              window.location.reload();
          }, 2000); // <-- time in milliseconds
           
          } else {
            alert(res.result);
          }
          $(".zbcp_loader").hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
          alert(thrownError);
          $(".zbcp_loader").hide();
        },
      });
    } else {
      alert("Please Enter Zipcode");
    }
    e.preventDefault();
  });
  /**
   * Ajax Request for inserting the multiple zipcodes into database
   */
  $(document).on("click", "#submit_s", function (e) {
    var zipCodesArray = [];
    var zipcodeVal = $("#zipcode_field_usa").val();
    if (zipcodeVal.length !== 0) {
      var obj = $("#zipcode_field_usa").select2("data");
      $.each(obj, function (key, value) {
        data = value.id + ':'+value.element.attributes[0].value+ ':' +value.element.attributes[1].value;
        zipCodesArray.push(
          // zipcode: value.id,
          // county: value.element.attributes[0].value,
          // city: value.element.attributes[1].value,
          data
        );
      });
      
      $(".zbcp_loader").show();
      jQuery.ajax({
        type: "POST",
        url: frontend_ajax_object.ajaxurl,
        data: {
          action: "insert_multiple_zipcode_into_database",
          zipCodesArray: zipCodesArray,
        },
        success: function (data) {
          
          var res = JSON.parse(data);
          if (res.status == true) {
             
            jQuery(".success-msg-zipcode").removeClass("display-success-msg");
            setTimeout(function() {
              jQuery('.success-msg-zipcode').fadeOut('slow');
              window.location.reload();
          }, 2000); // <-- time in milliseconds
          
          } else {
            alert(res.result);
          }
          $(".zbcp_loader").hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
          $(".zbcp_loader").hide();
          alert(thrownError);
        },
      });
    } else {
      alert("Please Enter Zipcode");
    }
    e.preventDefault();
  });
  /**
   * Select 2 initialization on the meta box
   */
  $("#post_page_zipcode_select").select2();
  $("#activate_zipcode_checkbox").change(function () {
    if ($(this).is(":checked")) {
      $(".activated_zipcode_checkbox").slideDown();
    } else {
      $(".activated_zipcode_checkbox").slideUp();
    }
  });
  /**
   * Function for creating the multistep form
   */
  $(document).ready(function () {
    var base_color = "rgb(230,230,230)";
    var active_color = "#16c3dc";
    var child = 1;
    var length = $("section").length - 1;
    $("#prev").addClass("disabled");
    $("#submit_s").addClass("disabled");
    $("section").not("section:nth-of-type(1)").hide();
    $("section")
      .not("section:nth-of-type(1)")
      .css("transform", "translateX(100px)");
    var svgWidth = length * 200 + 24;
    $("#svg_wrap").html(
      '<svg version="1.1" id="svg_form_time" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 ' +
        svgWidth +
        ' 24" xml:space="preserve"></svg>'
    );
    function makeSVG(tag, attrs) {
      var el = document.createElementNS("http://www.w3.org/2000/svg", tag);
      for (var k in attrs) el.setAttribute(k, attrs[k]);
      return el;
    }

    for (i = 0; i < length; i++) {
      var positionX = 12 + i * 200;
      var rect = makeSVG("rect", { x: positionX, y: 9, width: 200, height: 6 });
      document.getElementById("svg_form_time").appendChild(rect);
      // <g><rect x="12" y="9" width="200" height="6"></rect></g>'
      var circle = makeSVG("circle", {
        cx: positionX,
        cy: 12,
        r: 12,
        width: positionX,
        height: 6,
      });
      document.getElementById("svg_form_time").appendChild(circle);
    }

    var circle = makeSVG("circle", {
      cx: positionX + 200,
      cy: 12,
      r: 12,
      width: positionX,
      height: 6,
    });
    document.getElementById("svg_form_time").appendChild(circle);
    $("#svg_form_time rect").css("fill", base_color);
    $("#svg_form_time circle").css("fill", base_color);
    $("circle:nth-of-type(1)").css("fill", active_color);
    $(".button").click(function () {
      $("#svg_form_time rect").css("fill", active_color);
      $("#svg_form_time circle").css("fill", active_color);
      var id = $(this).attr("id");
      if (id == "next") {
        $("#prev").removeClass("disabled");
        if (child >= length) {
          $(this).addClass("disabled");
          $("#submit_s").removeClass("disabled");
        }
        if (child <= length) {
          child++;
        }
        var usa_state = $("#usa_state").val();
        var full_name_usa_state = $("#usa_state option:selected").text();
        $("#state_full_name").text(full_name_usa_state);
        console.log(full_name_usa_state);
        jQuery.ajax({
          type: "POST",
          url: frontend_ajax_object.ajaxurl,
          data: {
            action: "get_zipcode_from_api",
            usa_state: usa_state,
          },
          success: function (data) {
            var res = JSON.parse(data);
            if (res.status == true) {
              console.log(res.result);
              var options = [];
              var results = res.result;
              Object.entries(results).forEach(
                ([key, value]) =>
                  (options +=
                    '<option data-county="' +
                    value["county"] +
                    '" data-city="' +
                    value["city"] +
                    '" value="' +
                    value["zip_code"] +
                    '">' +
                    value["zip_code"] +
                    " " +
                    value["city"] +
                    " " +
                    value["county"] +
                    ", " +
                    value["state"] +
                    "</option>")
              );
              $("#zipcode_field_usa").html(options);
              $("#zipcode_field_usa").select2({
                dropdownParent: "#TB_window",
                width: "resolve",
              });
            } else {
              alert(res.result);
            }
          },
          error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError);
          },
        });
      } else if (id == "prev") {
        $("#next").removeClass("disabled");
        $("#submit_s").addClass("disabled");
        if (child <= 2) {
          $(this).addClass("disabled");
        }
        if (child > 1) {
          child--;
        }
      }
      var circle_child = child + 1;
      $("#svg_form_time rect:nth-of-type(n + " + child + ")").css(
        "fill",
        base_color
      );
      $("#svg_form_time circle:nth-of-type(n + " + circle_child + ")").css(
        "fill",
        base_color
      );
      var currentSection = $("section:nth-of-type(" + child + ")");
      currentSection.fadeIn();
      currentSection.css("transform", "translateX(0)");
      currentSection.prevAll("section").css("transform", "translateX(-100px)");
      currentSection.nextAll("section").css("transform", "translateX(100px)");
      $("section").not(currentSection).hide();
    });
  });
})(jQuery);
