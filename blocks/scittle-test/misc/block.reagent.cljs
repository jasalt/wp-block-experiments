;; ClojureScript block implementation using Scittle and Reagent
(require '[reagent.core :as r]
         '[reagent.dom :as rdom])

;; Store block states in a map keyed by clientId
(def block-states (atom {}))

;; Define a function to create the block editor
(let [blocks js/wp.blocks
      element js/wp.element
      blockEditor js/wp.blockEditor
      components js/wp.components
      el (.-createElement element)
      InspectorControls (.-InspectorControls blockEditor)
      useBlockProps (.-useBlockProps blockEditor)]

    (js/console.log "Registering block from ClojureScript with Reagent")

    ;; Get or create state for a specific block instance
    (defn get-block-state [client-id]
      (if-let [state (get @block-states client-id)]
        state
        (let [new-state (r/atom {:text-content ""
                                 :is-bold false})]
          (swap! block-states assoc client-id new-state)
          new-state)))

    ;; Preview component using Reagent
    (defn preview-component [state]
      (let [{:keys [text-content is-bold]} @state]
        (if is-bold
          [:strong {:key "bold-text"} text-content]
          [:p {:key "regular-text"} text-content])))

    ;; Inspector controls component using Reagent
    (defn inspector-component [state set-attributes]
      (let [{:keys [is-bold]} @state]
        [:> InspectorControls {:key "inspector"}
         [:> (.-PanelBody components) {:title "Text Settings"}
          [:> (.-ToggleControl components)
           {:label "Bold Text"
            :checked is-bold
            :onChange (fn [value]
                        (swap! state assoc :is-bold value)
                        (set-attributes #js {:isBold value}))}]]]))

    ;; Text control component using Reagent
    (defn text-control-component [state set-attributes]
      (let [{:keys [text-content]} @state]
        [:> (.-TextControl components)
         {:key "text-control"
          :label "Text Content"
          :value text-content
          :onChange (fn [value]
                      (swap! state assoc :text-content value)
                      (set-attributes #js {:textContent value}))}]))

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
                         clientId (.-clientId props)
                         textContent (or (.-textContent attributes) "")
                         isBold (or (.-isBold attributes) false)
                         block-state (get-block-state clientId)]

                     ;; Update local state when props change
                     (when (not= textContent (:text-content @block-state))
                       (swap! block-state assoc :text-content textContent))

                     (when (not= isBold (:is-bold @block-state))
                       (swap! block-state assoc :is-bold isBold))

                     ;; Create the main component with Reagent
                     (let [main-component
                           [:div (js->clj blockProps)
                            [inspector-component block-state setAttributes]
                            [text-control-component block-state setAttributes]
                            [:div {:key "preview" :className "preview"}
                             [preview-component block-state]]]]

                       ;; Convert Reagent component to React element
                       (r/as-element main-component))))

           :save (fn [] nil)}))
