function initTheme() {
  try {
    const savedTheme = localStorage.getItem('omniwire_theme') || localStorage.getItem('theme');
    if (savedTheme === 'light') {
      document.documentElement.classList.remove('dark');
    } else {
      document.documentElement.classList.add('dark');
    }
  } catch(e) {}
}

function toggleTheme() {
  const isDark = document.documentElement.classList.contains('dark');
  const isLight = isDark;
  if (isLight) {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('omniwire_theme', 'light');
    localStorage.setItem('theme', 'light');
  } else {
    document.documentElement.classList.add('dark');
    localStorage.setItem('omniwire_theme', 'dark');
    localStorage.setItem('theme', 'dark');
  }
}

const SUPPORTED_LANGUAGES = [
  { code: 'en', name: 'English', native: 'English', flag: '🇬🇧', dir: 'ltr' },
  { code: 'ur', name: 'Urdu', native: 'اردو', flag: '🇵🇰', dir: 'rtl' },
  { code: 'ar', name: 'Arabic', native: 'العربية', flag: '🇸🇦', dir: 'rtl' },
  { code: 'hi', name: 'Hindi', native: 'हिन्दी', flag: '🇮🇳', dir: 'ltr' },
  { code: 'de', name: 'German', native: 'Deutsch', flag: '🇩🇪', dir: 'ltr' },
  { code: 'fr', name: 'French', native: 'Français', flag: '🇫🇷', dir: 'ltr' },
  { code: 'es', name: 'Spanish', native: 'Español', flag: '🇪🇸', dir: 'ltr' },
  { code: 'it', name: 'Italian', native: 'Italiano', flag: '🇮🇹', dir: 'ltr' },
  { code: 'pt', name: 'Portuguese', native: 'Português', flag: '🇵🇹', dir: 'ltr' },
  { code: 'ru', name: 'Russian', native: 'Русский', flag: '🇷🇺', dir: 'ltr' },
  { code: 'zh-CN', name: 'Chinese', native: '简体中文', flag: '🇨🇳', dir: 'ltr' },
  { code: 'ja', name: 'Japanese', native: '日本語', flag: '🇯🇵', dir: 'ltr' },
  { code: 'ko', name: 'Korean', native: '한국어', flag: '🇰🇷', dir: 'ltr' },
  { code: 'tr', name: 'Turkish', native: 'Türkçe', flag: '🇹🇷', dir: 'ltr' },
  { code: 'id', name: 'Indonesian', native: 'Bahasa Indonesia', flag: '🇮🇩', dir: 'ltr' },
  { code: 'nl', name: 'Dutch', native: 'Nederlands', flag: '🇳🇱', dir: 'ltr' },
  { code: 'pl', name: 'Polish', native: 'Polski', flag: '🇵🇱', dir: 'ltr' },
  { code: 'sv', name: 'Swedish', native: 'Svenska', flag: '🇸🇪', dir: 'ltr' },
  { code: 'vi', name: 'Vietnamese', native: 'Tiếng Việt', flag: '🇻🇳', dir: 'ltr' },
  { code: 'fa', name: 'Persian', native: 'فارسی', flag: '🇮🇷', dir: 'rtl' }
];

function googleTranslateElementInit() {
  try {
    if (window.google && google.translate && typeof google.translate.TranslateElement === 'function') {
      new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: SUPPORTED_LANGUAGES.map(l => l.code).join(','),
        autoDisplay: false
      }, 'google_translate_element');
    }
  } catch(e) {}
}
window.googleTranslateElementInit = googleTranslateElementInit;

function setGoogleTranslateCookie(langCode) {
  const host = window.location.hostname;
  const isEn = (langCode === 'en' || !langCode);
  const cookieVal = isEn ? '' : ('/en/' + langCode);
  const expires = isEn 
    ? '=; expires=Thu, 01 Jan 1970 00:00:00 UTC;' 
    : ('=' + cookieVal + '; expires=' + new Date(Date.now() + 30 * 86400 * 1000).toUTCString() + ';');
  
  const domainParts = host.split('.');
  const domains = [
    '',
    host,
    '.' + host,
    (domainParts.length >= 2 ? '.' + domainParts.slice(-2).join('.') : '')
  ];
  const paths = ['/', window.location.pathname];
  
  domains.forEach(d => {
    paths.forEach(p => {
      document.cookie = 'googtrans' + expires + ' path=' + p + (d ? '; domain=' + d : '') + '; SameSite=Lax;';
    });
  });
}

function updateLanguageUI(langObj) {
  if (!langObj) return;
  if (langObj.dir === 'rtl') {
    document.documentElement.setAttribute('dir', 'rtl');
  } else {
    document.documentElement.setAttribute('dir', 'ltr');
  }
  const labels = document.querySelectorAll('.current-lang-label, #current-lang-label');
  labels.forEach(el => {
    if (langObj.code === 'en') {
      el.innerHTML = '🌐 20+ Languages';
    } else {
      el.innerHTML = `${langObj.flag} ${langObj.native || langObj.name}`;
    }
  });
}

function switchLanguage(langCode) {
  const langObj = SUPPORTED_LANGUAGES.find(l => l.code === langCode) || SUPPORTED_LANGUAGES[0];
  
  updateLanguageUI(langObj);
  setGoogleTranslateCookie(langCode);
  localStorage.setItem('omniwiremedia_lang', langCode);
  closeLanguageDropdown();
  if (typeof closeMobileMenu === 'function') {
    closeMobileMenu();
  }

  // If selecting English, restore original page
  if (langCode === 'en') {
    const combo = document.querySelector('.goog-te-combo');
    if (combo && combo.value !== 'en') {
      combo.value = 'en';
      ['change', 'input'].forEach(evt => {
        try { combo.dispatchEvent(new Event(evt, { bubbles: true })); } catch(e) {}
      });
    }
    setTimeout(() => {
      window.location.reload();
    }, 150);
    return;
  }

  // Trigger Google Translate combo element
  function tryTrigger() {
    const combo = document.querySelector('.goog-te-combo');
    if (combo) {
      combo.value = langCode;
      ['change', 'input'].forEach(evt => {
        try { combo.dispatchEvent(new Event(evt, { bubbles: true })); } catch(e) {}
      });
      return true;
    }
    return false;
  }

  if (!tryTrigger()) {
    let attempts = 0;
    const interval = setInterval(() => {
      attempts++;
      if (tryTrigger()) {
        clearInterval(interval);
      } else if (attempts >= 10) {
        clearInterval(interval);
        window.location.reload();
      }
    }, 150);
  }
}
window.switchLanguage = switchLanguage;

function toggleLanguageDropdown() {
  const dropdown = document.getElementById('lang-dropdown-menu');
  if (dropdown) {
    dropdown.classList.toggle('hidden');
  }
}

function closeLanguageDropdown() {
  const dropdown = document.getElementById('lang-dropdown-menu');
  if (dropdown) {
    dropdown.classList.add('hidden');
  }
}

document.addEventListener('click', function(e) {
  const container = document.getElementById('lang-selector-container');
  if (container && !container.contains(e.target)) {
    closeLanguageDropdown();
  }
});

// GLOBAL MOBILE MENU CONTROLLERS
function openMobileMenu() {
  const drawer = document.getElementById('mobile-menu-drawer') || document.getElementById('mobile-drawer') || document.querySelector('[data-menu-target]');
  const backdrop = document.getElementById('mobile-drawer-backdrop') || document.querySelector('[data-menu-backdrop]');
  if (drawer) {
    drawer.classList.remove('translate-x-full', 'drawer-closed');
    drawer.classList.add('translate-x-0', 'drawer-open');
  }
  if (backdrop) {
    backdrop.classList.remove('hidden');
  }
  document.body.classList.add('overflow-hidden');
}

function closeMobileMenu() {
  const drawer = document.getElementById('mobile-menu-drawer') || document.getElementById('mobile-drawer') || document.querySelector('[data-menu-target]');
  const backdrop = document.getElementById('mobile-drawer-backdrop') || document.querySelector('[data-menu-backdrop]');
  if (drawer) {
    drawer.classList.add('translate-x-full', 'drawer-closed');
    drawer.classList.remove('translate-x-0', 'drawer-open');
  }
  if (backdrop) {
    backdrop.classList.add('hidden');
  }
  document.body.classList.remove('overflow-hidden');
}

function toggleMobileMenu() {
  const drawer = document.getElementById('mobile-menu-drawer') || document.getElementById('mobile-drawer') || document.querySelector('[data-menu-target]');
  if (drawer && (drawer.classList.contains('translate-x-0') || drawer.classList.contains('drawer-open'))) {
    closeMobileMenu();
  } else {
    openMobileMenu();
  }
}

window.openMobileMenu = openMobileMenu;
window.closeMobileMenu = closeMobileMenu;
window.toggleMobileMenu = toggleMobileMenu;

function initNavigationAndIcons() {
  // Bind synchronous mobile menu toggle listeners
  const toggleBtns = document.querySelectorAll('#mobile-menu-btn, [data-menu-toggle]');
  toggleBtns.forEach(btn => {
    btn.onclick = function(e) {
      if (e) e.preventDefault();
      toggleMobileMenu();
    };
  });

  // Bind synchronous close buttons & backdrop click
  const closeBtns = document.querySelectorAll('[data-menu-close], #mobile-drawer-backdrop');
  closeBtns.forEach(btn => {
    btn.onclick = function(e) {
      if (e) e.preventDefault();
      closeMobileMenu();
    };
  });

  // Escape key handler
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMobileMenu();
  });

  // Synchronous Lucide initialization on first paint
  if (window.lucide && typeof lucide.createIcons === 'function') {
    lucide.createIcons();
  }

  // Restore saved language preference UI
  const savedLang = localStorage.getItem('omniwiremedia_lang');
  if (savedLang && savedLang !== 'en') {
    const langObj = SUPPORTED_LANGUAGES.find(l => l.code === savedLang);
    if (langObj) {
      updateLanguageUI(langObj);
      setGoogleTranslateCookie(savedLang);
      
      // Ensure combo value is synchronized
      let checkCount = 0;
      const chk = setInterval(() => {
        checkCount++;
        const combo = document.querySelector('.goog-te-combo');
        if (combo) {
          clearInterval(chk);
          if (combo.value !== savedLang) {
            combo.value = savedLang;
            ['change', 'input'].forEach(evt => {
              try { combo.dispatchEvent(new Event(evt, { bubbles: true })); } catch(e) {}
            });
          }
        } else if (checkCount >= 15) {
          clearInterval(chk);
        }
      }, 200);
    }
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initNavigationAndIcons);
} else {
  initNavigationAndIcons();
}

window.addEventListener('load', function() {
  if (window.lucide && typeof lucide.createIcons === 'function') {
    lucide.createIcons();
  }
});