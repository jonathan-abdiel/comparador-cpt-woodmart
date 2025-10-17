(function($){
  function renderResults(container, items){
    container.empty();
    if(!items || !items.length){
      container.append('<div class="notice-inline">Sin resultados.</div>');
      return;
    }
    items.forEach(function(it){
      const row = $('<div class="result">\
        <span class="r-title"></span>\
        <button type="button" class="button button-small add">Agregar</button>\
      </div>');
      row.data('item', it);
      row.find('.r-title').text(it.text + ' (ID: ' + it.id + ')');
      container.append(row);
    });
  }

  function addSelected(list, id, text){
    // Evitar duplicados
    let exists = false;
    list.find('li').each(function(){
      if (parseInt($(this).data('id'), 10) === parseInt(id, 10)) exists = true;
    });
    if (exists) return;

    const li = $('<li><span class="title"></span><button type="button" class="button-link compare-remove">&times;</button><input type="hidden" name="compare_products[]"></li>');
    li.attr('data-id', id);
    li.find('.title').text(text);
    li.find('input').val(id);
    list.append(li);
  }

  $(function(){
    const $input   = $('#compare-search-input');
    const $btn     = $('#compare-search-btn');
    const $results = $('#compare-search-results');
    const $chosen  = $('#compare-selected');

    $chosen.sortable({ placeholder: "ui-state-highlight" });

    $btn.on('click', function(){
      const q = $input.val().trim();
      $results.html('<em>Buscando...</em>');
      $.get(CompareAdmin.ajaxurl, {
        action: 'compare_search_products',
        nonce: CompareAdmin.nonce,
        q: q,
        page: 1
      }).done(function(resp){
        if(resp && resp.success){
          renderResults($results, resp.data.items);
        } else {
          $results.html('<div class="error">Error en la búsqueda</div>');
        }
      }).fail(function(){
        $results.html('<div class="error">Error en la búsqueda</div>');
      });
    });

    $results.on('click', '.add', function(){
      const it = $(this).closest('.result').data('item');
      addSelected($chosen, it.id, it.text);
    });

    $chosen.on('click', '.compare-remove', function(){
      $(this).closest('li').remove();
    });
  });
})(jQuery);
