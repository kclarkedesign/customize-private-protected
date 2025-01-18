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

jQuery(document).ready(function ($) {
  var is_private = $("body").hasClass("is-private");
  var is_protected = $("body").hasClass("is-protected");

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
        $(".protected-intro-text").text(newval);
      });
    });
  }
});
