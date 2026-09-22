<div class="search-modal" id="searchModal">
    <div class="close-search" id="closeSearch">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <a href="{{ route('home') }}" class="search-modal-logo" aria-label="Coffee & Kala home">
        <img
            src="{{ \App\Support\BrandLogo::url() }}"
            alt="Coffee & Kala"
            width="{{ \App\Support\BrandLogo::width() }}"
            height="{{ \App\Support\BrandLogo::height() }}"
        >
    </a>

    <div class="search-input-box">
        <form class="search-form" id="searchForm" action="{{ route('search') }}" role="search" autocomplete="off">
            <input
                type="text"
                name="q"
                id="searchInput"
                class="search-input"
                placeholder="Search by articles, journals, poetries, gallery and studio pictures"
                aria-label="Site search"
                aria-expanded="false"
                aria-controls="searchResults"
                role="combobox"
                autocomplete="off"
                autofocus
            >
            <span class="search-input-hint" id="searchInputHint" aria-hidden="true"></span>
            <i class="fa-solid fa-magnifying-glass search-input-icon" aria-hidden="true"></i>
            <span class="search-input-spinner" aria-hidden="true"></span>
        </form>

        <div class="search-results" id="searchResults" role="listbox" hidden></div>
    </div>
</div>
