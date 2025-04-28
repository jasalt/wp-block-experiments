;; ClojureScript block implementation using Scittle and Reagent
(require '[reagent.core :as r])

;; Define a function to create the block editor
(let [blocks js/wp.blocks
      element js/wp.element
      blockEditor js/wp.blockEditor
      components js/wp.components
      InspectorControls (.-InspectorControls blockEditor)
      useBlockProps (.-useBlockProps blockEditor)
      TextControl (.-TextControl components)
      ToggleControl (.-ToggleControl components)
      PanelBody (.-PanelBody components)]

    (js/console.log "Registering block from ClojureScript with Reagent")

    ;; Register the block
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
                     
                     ;; Create the component with Reagent hiccup syntax
                     (r/as-element
                      [:div (js->clj blockProps)
                       ;; Inspector Controls
                       [:> InspectorControls {:key "inspector"}
                        [:> PanelBody {:title "Text Settings"}
                         [:> ToggleControl
                          {:label "Bold Text"
                           :checked isBold
                           :onChange (fn [value]
                                       (setAttributes #js {:isBold value}))}]]]
                       
                       ;; Text Control
                       [:> TextControl
                        {:key "text-control"
                         :label "Text Content"
                         :value textContent
                         :onChange (fn [value]
                                     (setAttributes #js {:textContent value}))}]
                       
                       ;; Preview
                       [:div {:key "preview" :className "preview"}
                        (if isBold
                          [:strong {:key "bold-text"} textContent]
                          [:p {:key "regular-text"} textContent])]])))

           :save (fn [] nil)}))
