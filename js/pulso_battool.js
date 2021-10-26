(function ($) {
  Drupal.behaviors.PulsoBattoolBehavior = {
    attach: function (context, settings) {
      $('img.bat-tool-gauge').once("PulsoBattoolBehavior").each((i, e) => {
        const $img = $(e);
        const imgID = $img.attr('id');
        const imgClass = $img.attr('class');
        const imgURL = $img.attr('src');
        $.get(imgURL, (data) => {
          let $svg = $(data).find('svg');
          if (typeof imgID !== 'undefined') {
            $svg = $svg.attr('id', imgID);
          }
          if ($img.data('bat-tool-norm')) {
            $svg.find('.norm:not(.' + $img.data('bat-tool-norm') + ')').css('display', 'none');
          }
          if ($img.data('bat-tool-score')) {
            let deg = 180 / 5 * $img.data('bat-tool-score');
            $svg.find('#batToolScore').css('transform', 'rotate(' + deg + 'deg)');
          }
          if ($img.data('bat-tool-risk-profile')) {
            $svg.find('#batToolRiskProfile').text($img.data('bat-tool-risk-profile'));
          }
          if (typeof imgClass !== 'undefined') {
            $svg = $svg.attr('class', `${imgClass} replaced-svg`);
          }
          $svg = $svg.removeAttr('xmlns:a');
          if (!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
            $svg.attr(`viewBox 0 0  ${$svg.attr('height')} ${$svg.attr('width')}`);
          }
          $img.replaceWith($svg);
        }, 'xml');
      });

      $(document).on('change', '.bat-tool-tr input[type=radio]', function (e) {
        $(this).closest('.bat-tool-tr').find('input[type=radio].is-invalid').removeClass('error is-invalid').removeAttr('aria-invalid');
      });
    }
  };
})(jQuery);
