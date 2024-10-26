jQuery(document).ready(function () {
  jQuery(document).on("click", "#flexi_ajax_delete", function (e) {
    e.preventDefault();
    post_id = jQuery(this).attr("data-post_id");
    media_id = jQuery(this).attr("data-media_id");
    //alert(post_id+'--'+media_id);
    nonce = jQuery(this).attr("data-nonce");
    var x = confirm(myAjax.delete_string);
    if (x) {
      jQuery.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: { action: "flexi_ajax_delete", post_id: post_id, nonce: nonce, media_id:media_id },
        success: function (response) {
          if (response.type == "success") {
            if(media_id)
            {
              jQuery("#flexi_media_" + media_id).slideUp("slow");
            }
            else
            {
              jQuery("#flexi_content_" + post_id).slideUp("slow");
              jQuery("#flexi_" + post_id).slideUp();
            }
            
                        
          } else {
            alert("Deleted: " + post_id);
          }
        },
      });
    }
  });
  jQuery("#abc").click(function (e) {
    //alert("hi");
  });
  jQuery(".xyz").click(function (e) {
    // alert("bye");
  });
});
