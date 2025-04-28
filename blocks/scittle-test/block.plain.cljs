;; Plain ClojureScript version without Reagent

;; Define a function to create the block editor
(let [blocks js/wp.blocks
      element js/wp.element
      blockEditor js/wp.blockEditor
      components js/wp.components
      el (.-createElement element)
      TextControl (.-TextControl components)
      ToggleControl (.-ToggleControl components)
      PanelBody (.-PanelBody components)
      InspectorControls (.-InspectorControls blockEditor)
      useBlockProps (.-useBlockProps blockEditor)]

    (js/console.log "Registering block from ClojureScript")

    (.registerBlockType blocks
      "my-plugin/scittle-block"
      #js {:title "Scittle Block"
           :icon "text"
           :category "text"

           :edit (fn [props]
                   (let [attributes (.-attributes props)
                         setAttributes (.-setAttributes props)
                         blockProps (useBlockProps)
                         textContent (or (.-textContent attributes) "")
                         isBold (or (.-isBold attributes) false)]

                     (el "div" blockProps
                         #js [(el InspectorControls #js {:key "inspector"}
                                 (el PanelBody #js {:title "Text Settings"}
                                     (el ToggleControl
                                         #js {:label "Bold Text"
                                              :checked isBold
                                              :onChange (fn [value]
                                                          (setAttributes #js {:isBold value}))})))

                              (el TextControl
                                  #js {:key "text-control"
                                       :label "Text Content"
                                       :value textContent
                                       :onChange (fn [value]
                                                   (setAttributes #js {:textContent value}))})

                              (el "div" #js {:key "preview" :className "preview"}
                                  (if isBold
                                    (el "strong" #js {:key "bold-text"} textContent)
                                    (el "p" #js {:key "regular-text"} textContent)))])))
           :save (fn [] nil)}))
