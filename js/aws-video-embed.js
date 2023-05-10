(function($) {
  $.entwine('ss', function($) {

    $('select.js-aws-video-field').entwine({
      onchange: function() {
          let $urlField = $('input.js-url-embed-field');
          let target = $urlField[0];
          if (this.context.value) {
            Object.defineProperty(target, 'value', {value: this.context.value});
            $urlField.attr('disabled', true);
          } else {
            Object.defineProperty(target, 'value', {value: ''});
            $urlField.attr('disabled', false);
          }
          const event = new Event("input", {bubbles: true});
          target.dispatchEvent(event);
      }
    });

  });
})(jQuery);
