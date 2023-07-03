var pageTitle;

// Function to escape special characters in a string for use in a regular expression
function escapeRegExp(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"); // $& means the whole matched string
}

wp.customize.bind("preview-ready", function () {
  var title = jQuery(".entry-title").text() || jQuery("body > h1").text();
  var prefixProtected = pageData.prefixProtected;
  var prefixPrivate = pageData.prefixPrivate;
  var regexProtected = new RegExp("^" + escapeRegExp(prefixProtected));
  var regexPrivate = new RegExp("^" + escapeRegExp(prefixPrivate));
  if (regexProtected.test(title)) {
    pageTitle = title.replace(regexProtected, "");
  } else if (regexPrivate.test(title)) {
    pageTitle = title.replace(regexPrivate, "");
  } else {
    pageTitle = title.trim();
  }
});

// jQuery(window).on("load", function () {
//   // Create the loader element
//   var loader = jQuery(".custom_loading");
//   console.log(loader);

//   // Append the loader next to each control that should trigger it
//   jQuery(".customize-control").append(loader.clone());

//   // When a control's value is changed, show the loader next to it
//   jQuery(".customize-control").change(function () {
//     jQuery(this).find(".custom_loading").show();
//   });
// });

jQuery(document).ready(function ($) {
  var is_private = $("body").hasClass("is-private");
  var is_protected = $("body").hasClass("is-protected");

  // When the customizer finishes loading (you'll need to implement this yourself), hide the loader
  // This is just an example, the actual implementation may vary depending on how your customizer works
  // $(document).on("customizer-loaded", function () {
  //   $(".custom_loading").hide();
  // });

  if (is_private || is_protected) {
    var prevPrefix = is_protected
      ? wp.customize("cpp_prefix_protected").get()
      : wp.customize("cpp_prefix_private").get();

    function handlePrefix(customizeElem, prevval, newval) {
      var hidePrefix = wp.customize("cpp_hide_prefix").get();
      var $titleElement = jQuery(".entry-title").length
        ? jQuery(".entry-title")
        : jQuery("body > h1");

      if (jQuery("wp-block-post-title").length) {
        $titleElement = jQuery("wp-block-post-title");
      }
      // console.log(customizeElem + " new value: ", newval);

      if (!hidePrefix) {
        updatedTitle =
          (is_protected
            ? wp.customize("cpp_prefix_protected").get()
            : wp.customize("cpp_prefix_private").get()) +
          " " +
          pageTitle;

        $titleElement.text(updatedTitle);
      }

      prevval = newval;
    }

    // wp.customize("cpp_hide_prefix", function (value) {
    //   value.bind(function (newval) {
    //     var updatedTitle = "";
    //     var $titleElement = jQuery(".entry-title").length
    //       ? jQuery(".entry-title")
    //       : jQuery("body > h1");

    //     console.log("cpp_hide_prefix new value: ", newval);

    //     if (!newval) {
    //       updatedTitle =
    //         (is_protected
    //           ? wp.customize("cpp_prefix_protected").get()
    //           : wp.customize("cpp_prefix_private").get()) +
    //         " " +
    //         pageTitle;
    //     } else {
    //       updatedTitle = $titleElement.text().replace(prevPrefix, "");
    //     }

    //     $titleElement.text(updatedTitle);
    //   });
    // });

    wp.customize("cpp_prefix_private", function (value) {
      value.bind(function (newval) {
        handlePrefix("cpp_prefix_private", prevPrefix, newval);
      });
    });

    wp.customize("cpp_prefix_protected", function (value) {
      value.bind(function (newval) {
        handlePrefix("cpp_prefix_protected", prevPrefix, newval);
      });
    });

    wp.customize("cpp_text_intro", function (value) {
      value.bind(function (newval) {
        // console.log("cpp_text_intro new value: ", newval);
        $(".protected-intro-text").text(newval);
      });
    });
  }
});
