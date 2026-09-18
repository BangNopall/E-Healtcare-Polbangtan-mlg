const setup = () => {
    const getTheme = () => {
        if (window.localStorage.getItem("dark")) {
            return JSON.parse(window.localStorage.getItem("dark"));
        }
        return (
            !!window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches
        );
    };

    const setTheme = (value) => {
        window.localStorage.setItem("dark", value);
    };

    const getColor = () => {
        if (window.localStorage.getItem("color")) {
            return window.localStorage.getItem("color");
        }
        return "blue";
    };

    const setColors = (color) => {
        const root = document.documentElement;
        root.style.setProperty("--color-primary", `var(--color-blue)`);
        root.style.setProperty("--color-primary-50", `var(--color-blue-50)`);
        root.style.setProperty("--color-primary-100", `var(--color-blue-100)`);
        root.style.setProperty(
            "--color-primary-light",
            `var(--color-blue-light)`
        );
        root.style.setProperty(
            "--color-primary-lighter",
            `var(--color-blue-lighter)`
        );
        root.style.setProperty(
            "--color-primary-dark",
            `var(--color-blue-dark)`
        );
        root.style.setProperty(
            "--color-primary-darker",
            `var(--color-blue-darker)`
        );
        this.selectedColor = color;
        window.localStorage.setItem("color", color);
    };

    return {
        loading: true,
        isDark: getTheme(),
        color: getColor(),
        toggleTheme() {
            this.isDark = !this.isDark;
            setTheme(this.isDark);
        },
        setColors,
    };
};
