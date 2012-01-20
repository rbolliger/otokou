  public function executeIndex(sfWebRequest $request)
  {
    // sorting
    if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort')))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }

    // pager
    if ($request->getParameter('page'))
    {
      $this->setPage($request->getParameter('page'));
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();
    
    
    // filters appearance
    if ($request->getParameter('filters_appearance') && in_array($request->getParameter('filters_appearance'), array('hidden', 'show'))) {
            $this->setFiltersAppearance($request->getParameter('filters_appearance'));
     }

    $this->pager->form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);

    $this->filters_appearance = $this->getFiltersAppearance();
  }
