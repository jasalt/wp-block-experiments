;; ClojureScript block implementation using Scittle and Reagent
(require '[reagent.core :as r])

;; Store block states in a map keyed by clientId
(def block-states (atom {}))

;; Access WordPress components directly
(def wp-blocks js/wp.blocks)
(def wp-block-editor js/wp.blockEditor)
(def wp-components js/wp.components)

;; Get or create state for a specific block instance
(defn get-block-state [client-id]
  (if-let [state (get @block-states client-id)]
    state
    (let [new-state (r/atom {:text-content ""
                             :is-bold false})]
      (swap! block-states assoc client-id new-state)
      new-state)))

;; Block edit function
(defn edit-block [props]
  (let [attributes (.-attributes props)
        set-attributes (.-setAttributes props)
        block-props (wp-block-editor.useBlockProps)
        client-id (.-clientId props)
        text-content (or (.-textContent attributes) "")
        is-bold (or (.-isBold attributes) false)
        block-state (get-block-state client-id)]

    ;; Update local state when props change
    (when (not= text-content (:text-content @block-state))
      (swap! block-state assoc :text-content text-content))

    (when (not= is-bold (:is-bold @block-state))
      (swap! block-state assoc :is-bold is-bold))

    ;; Create the component with Reagent hiccup syntax
    (r/as-element
     [:div (js->clj block-props)
      ;; Inspector Controls
      [:> wp-block-editor.InspectorControls {:key "inspector"}
       [:> wp-components.PanelBody {:title "Text Settings"}
        [:> wp-components.ToggleControl
         {:label "Bold Text"
          :checked (:is-bold @block-state)
          :onChange (fn [value]
                      (swap! block-state assoc :is-bold value)
                      (set-attributes #js {:isBold value}))}]]]

      ;; Text Control
      [:> wp-components.TextControl
       {:key "text-control"
        :label "Text Content"
        :value (:text-content @block-state)
        :onChange (fn [value]
                    (swap! block-state assoc :text-content value)
                    (set-attributes #js {:textContent value}))}]

      ;; Preview
      [:div {:key "preview" :className "preview"}
       (if (:is-bold @block-state)
         [:strong {:key "bold-text"} (:text-content @block-state)]
         [:p {:key "regular-text"} (:text-content @block-state)])]])))

;; Register the block
(js/console.log "Registering block from ClojureScript with Reagent")

(.registerBlockType wp-blocks
  "my-plugin/scittle-block"
  #js {:title "Scittle Block"
       :icon "text"
       :category "text"
       :edit edit-block
       :save (fn [] nil)})
