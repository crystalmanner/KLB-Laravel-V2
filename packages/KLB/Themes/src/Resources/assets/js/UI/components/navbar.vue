<template>
  <nav
    class="navbar navbar-default navbar-expand-lg"
    v-if="slicedCategories && slicedCategories.length > 0"
  >
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul
        class="navbar-nav navbar-expand-lg"
        style="
          text-align: justify;
          color: white;
          max-height: 50%;
          max-width: 100%;
          margin: 0 auto;
          padding: 5px;
        "
      >
        <li
          class="nav-item dropdown category-title"
          :key="categoryIndex"
          :id="`category-${category.id}`"
          style="color: white; position: inherit !important"
          v-for="(category, categoryIndex) in slicedCategories"
        >
          <a
            class="nav-link dropdown-toggle category-title"
            :href="`${$root.baseUrl}/${category.slug}`"
            data-toggle="dropdown"
            aria-haspopup="true"
            style="color: white"
            aria-expanded="true"
          >
            <!--Clearence-->
            <span style="color: white; font-size: 15px; font-family: Georgia">{{
              category["name"]
            }}</span>

            <!--<span>hello class="category-title" </span>-->
          </a>

          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink" style="width: 100%;">
            <a
              class="dropdown-item"
              :href="`${$root.baseUrl}/${category.slug}`"
            >
              <span style="font-family: Georgia; font-weight: bold">{{
                category["name"]
              }}</span>
            </a>
            <div style="display: flex; right: 0px;">
              <li
                v-if="category.children.length && category.children.length > 0"
                :key="`${subCategoryIndex}-${categoryIndex}`"
                v-for="(subCategory, subCategoryIndex) in category.children"
              >
                <a
                  class="dropdown-item"
                  :class="`category sub-category unset ${
                    subCategory.children.length > 0 ? 'fw6' : ''
                  }`"
                  :href="`${$root.baseUrl}/${category.slug}/${subCategory.slug}`"
                >
                  <span style="font-family: Georgia; font-weight: bold">{{
                    subCategory["name"]
                  }}</span>
                </a>

                <ul type="none" class="nested">
                  <li
                    :key="`${childSubCategoryIndex}-${subCategoryIndex}-${categoryIndex}`"
                    v-for="(
                      childSubCategory, childSubCategoryIndex
                    ) in subCategory.children"
                  >
                    <a
                      :id="`sidebar-level-link-3-${childSubCategoryIndex}`"
                      :class="`category unset ${
                        subCategory.children.length > 0 ? 'fw6' : ''
                      }`"
                      :href="`${$root.baseUrl}/${category.slug}/${subCategory.slug}/${childSubCategory.slug}`"
                    >
                      <span style="font-weight: lighter">{{
                        childSubCategory.name
                      }}</span>
                    </a>
                  </li>
                </ul>
              </li>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</template>







<script>
export default {
  props: ["id", "addClass", "parentSlug", "mainSidebar", "categoryCount"],

  data: function () {
    return {
      slicedCategories: [],
      sidebarLevel: Math.floor(Math.random() * 1000),
    };
  },

  watch: {
    "$root.sharedRootCategories": function (categories) {
      this.formatCategories(categories);
    },
  },

  methods: {
    remainBar: function (id) {
      let sidebar = $(`#${id}`);
      if (sidebar && sidebar.length > 0) {
        sidebar.show();

        let actualId = id.replace("sidebar-level-", "");

        let sidebarContainer = sidebar.closest(`.sub-category-${actualId}`);
        if (sidebarContainer && sidebarContainer.length > 0) {
          sidebarContainer.show();
        }
      }
    },

    formatCategories: function (categories) {
      let slicedCategories = categories;
      let categoryCount = this.categoryCount ? this.categoryCount : 9;

      if (slicedCategories && slicedCategories.length > categoryCount) {
        slicedCategories = categories.slice(0, categoryCount);
      }

      if (this.parentSlug) slicedCategories["parentSlug"] = this.parentSlug;

      this.slicedCategories = slicedCategories;
    },
  },
};
</script>


