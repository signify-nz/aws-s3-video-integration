(function($) {
  $.entwine('ss', function($) {

    // Set value for React object
    const setNativeValue = (element, value) => {
      const valueSetter = Object.getOwnPropertyDescriptor(element, 'value').set;
      const prototype = Object.getPrototypeOf(element);
      const prototypeValueSetter = Object.getOwnPropertyDescriptor(prototype, 'value').set;

      if (valueSetter && valueSetter !== prototypeValueSetter) {
          prototypeValueSetter.call(element, value);
      } else {
          valueSetter.call(element, value);
      }
    };

    $('select.js-aws-video-field').entwine({
      onchange: function() {
          let $urlField = $('input.js-url-embed-field');
          let target = $urlField[0];
          if (this.context.value) {
            setNativeValue(target, this.context.value);
            $urlField.attr('disabled', true);
          } else {
            setNativeValue(target, '');
            $urlField.attr('disabled', false);
          }
          const event = new Event("input", {bubbles: true});
          target.dispatchEvent(event);
      }
    });

  });
})(jQuery);
