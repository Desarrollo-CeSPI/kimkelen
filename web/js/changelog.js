function changelog_render_tooltip(url, klass, id, html_id)
{
  jQuery.ajax({
      url: url,
      data: "klass="+klass+"&id="+id,
      cache: false,
      error:        function(xhr, status, error) { alert(xhr.status); },
      success:      function(data)
                    {
                      jQuery(html_id).html(data);
                      jQuery(html_id).fadeIn(500);
                    }
      });
}
