export default new class Theme {
    set(theme) {
        theme = theme || this.get();
        localStorage.theme = theme;
        theme === 'light'
            ? document.documentElement.classList.remove('dark')
            : document.documentElement.classList.add('dark');
    }

    get() {
        return localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
            ? 'dark'
            : 'light';
    }
}
