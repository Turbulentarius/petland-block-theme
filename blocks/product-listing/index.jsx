import { registerBlockType } from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, RangeControl } from "@wordpress/components";
import { useEffect, useState } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";
import './style.css';

registerBlockType("petland/product-listing", {
  title: __("Product Listing (Petland)", "petlandtextdomain"),
  icon: "admin-post",
  category: "widgets",
  attributes: {
    postsToShow: { type: "number", default: 6 },
  },
  edit: (props) => {
    const { attributes, setAttributes } = props;
    const { postsToShow } = attributes;
    const blockProps = useBlockProps();

    const [preview, setPreview] = useState("");

    useEffect(() => {
      apiFetch({
        path: "/wp/v2/block-renderer/petland/product-listing",
        method: "POST",
        data: {
          context: "edit",
          attributes: { postsToShow },
        },
      }).then((res) => setPreview(res.rendered));
    }, [postsToShow]);

    return (
      <div {...blockProps}>
        <InspectorControls>
          <PanelBody title={__("Settings", "petlandtextdomain")}>
            <RangeControl
              label={__("Products to show", "petlandtextdomain")}
              min={1}
              max={50}
              value={postsToShow}
              onChange={(value) => setAttributes({ postsToShow: value })}
            />
          </PanelBody>
        </InspectorControls>
        <div
          dangerouslySetInnerHTML={{
            __html: preview || "<p>Loading preview...</p>",
          }}
        />
      </div>
    );
  },

  save() {
    return null; // rendered in PHP
  },
});
