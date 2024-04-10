const defaultSearchConfiguration = {
    _searchable: true,
    name: {
        _searchable: true,
        _score: 500,
    },
    teaser: {
        _searchable: true,
        _score: 500,
    },
    content: {
        _searchable: true,
        _score: 250,
    },
};

export default defaultSearchConfiguration;
