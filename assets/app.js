(function(){
  function showPage(){
    document.documentElement.classList.add('js-enabled');
    document.body.classList.add('page-visible');
  }

  function interceptLinks(){
    document.addEventListener('click', function(e){
      var a = e.target.closest('a');
      if(!a) return;
      var href = a.getAttribute('href');
      if(!href) return;
      if(a.target === '_blank' || href.startsWith('mailto:') || href.startsWith('tel:')) return;
      if(href.startsWith('#') || href.indexOf(location.pathname) !== -1 && href.indexOf('?')===-1 && href.split('#')[0] === location.pathname){
        return;
      }
      var isExternal = (a.hostname && a.hostname !== location.hostname);
      if(isExternal) return;

      e.preventDefault();
      document.body.classList.remove('page-visible');
      setTimeout(function(){
        window.location = href;
      }, 260);
    }, false);
  }

  function protectForms(){
    document.addEventListener('submit', function(e){
      var form = e.target;
      if(!form || !(form.tagName === 'FORM')) return;
      var submits = form.querySelectorAll('button[type="submit"], input[type="submit"]');
      if(form._submitted) {
        e.preventDefault();
        return;
      }
      form._submitted = true;
      submits.forEach(function(btn){
        btn.disabled = true;
        var loading = btn.getAttribute('data-loading') || 'Przetwarzanie...';
        btn._orig = btn.textContent;
        btn.textContent = loading;
      });
    }, true);
  }

  function flashFromQuery(){
    try {
      var params = new URLSearchParams(location.search);
      if(params.has('msg')){
        var text = params.get('msg');
        if(text){
          showToast(decodeURIComponent(text));
          history.replaceState({}, '', location.pathname + location.hash);
        }
      }
    } catch(e){}
  }

  function showToast(message, ttl){
    ttl = ttl || 4200;
    var wrap = document.createElement('div');
    wrap.className = 'simple-toast';
    wrap.style.position = 'fixed';
    wrap.style.right = '20px';
    wrap.style.bottom = '20px';
    wrap.style.background = 'rgba(10,14,22,0.9)';
    wrap.style.padding = '12px 16px';
    wrap.style.borderRadius = '10px';
    wrap.style.boxShadow = '0 8px 30px rgba(2,6,23,0.6)';
    wrap.style.color = '#fff';
    wrap.style.fontSize = '14px';
    wrap.style.zIndex = 1200;
    wrap.textContent = message;
    document.body.appendChild(wrap);
    setTimeout(function(){ wrap.style.opacity = '0'; wrap.style.transform = 'translateY(8px)';}, ttl-400);
    setTimeout(function(){ document.body.removeChild(wrap); }, ttl);
  }

  function ensureSkipLink(){
    if(document.getElementById('skip-main')) return;
    var a = document.createElement('a');
    a.href = '#main';
    a.id = 'skip-main';
    a.textContent = 'Przejdź do treści';
    a.style.position = 'absolute';
    a.style.left = '-999px';
    a.style.top = 'auto';
    a.style.height = '1px';
    a.style.width = '1px';
    a.style.overflow = 'hidden';
    a.style.zIndex = 2000;
    a.addEventListener('focus', function(){ a.style.left = '8px'; a.style.top = '8px'; a.style.background = '#fff'; a.style.color='#000'; a.style.padding='8px'; a.style.width='auto'; a.style.height='auto';});
    a.addEventListener('blur', function(){ a.style.left = '-999px'; a.style.top = 'auto';});
    document.body.insertBefore(a, document.body.firstChild);
  }

  document.addEventListener('DOMContentLoaded', function(){
    setTimeout(showPage, 20);
    interceptLinks();
    protectForms();
    flashFromQuery();
    ensureSkipLink();
  });

  window.OP = window.OP || {};
  window.OP.showToast = showToast;

})();