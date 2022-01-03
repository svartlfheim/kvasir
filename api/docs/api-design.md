# API Design

We'll follow a pretty basic REST API setup, using https://docs.microsoft.com/en-us/azure/architecture/best-practices/api-design as a template.

## FOS rest bundle

I wanted to use the body listener from the fos rest bundle as it will make the conversion of request to DTO alot more succinct. At the time the fos rest bundle does not work with symfony 6, outside of the development versions. This is fine for now, so we've specifically installed a less stable version than we'd like.

It seems a little overkill to install the full bundle just for this, but it would be a pain to maintain the code to handle this as well. This again, will be fine for now, we may have use for some of the other features later.

Also had to install jms serializer as it's required by the fos rest bundle