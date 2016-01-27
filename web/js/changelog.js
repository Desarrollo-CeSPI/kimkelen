function changelog_render_tooltip(url, klass, id, html_id)
{
    var $el = jQuery(html_id);
    if ($el.data('present')) {
        $el.fadeToggle('fast');
      } else {
          jQuery.ajax({
                url: url,
                data: { klass: klass, id: id },
                cache: false,
                error:    function(xhr, status, error) { alert(xhr.status); },
                success:  function(data) {
                              $el.html(data)
                               .fadeIn('fast')
                               .data('present', true);
                        }
              });
        }
}
