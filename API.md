## Virtualjog WordPress Plugin API Documentation

Since virtualjog is a closed source SaaS we can only disclose endpoints and parameters related to this plugin
Unfortunately, most of the materials connected to this software are in hungarian, and there are no official english documentation available yet.

Please refer to the ABOUT.md file for more information about the plugin and the software itself.

The Privacy Policy and Terms of Service documents are available here
- https://virtualjog.hu/altalanos-szerzodesi-feltetelek/ (ToS)
- https://virtualjog.hu/adatvedelmi-szabalyzat/ (Privacy Policy)

Virtualjog clients have already accepted these documents when they signed up for the service.
Due to limitations, we cannot provide an english version of these documents, however, there is only one thing users input in the plugin, and that is the access token.

The access token is a unique identifier that is generated when the user registers for the service. 
This token is used to authenticate the user and provide the necessary data for the plugin to work.
This does not qualify as personal information.

There is one more data that is sent to the API, and that is the current domain name, obtained from the `SERVER_NAME`. 
This is used to generate the cookie script for the user's website.
This is also not considered personal information.

## Endpoints
- api.virtualjog.hu/api/v1/wordpress-authorize
- api.virtualjog.hu/api/v1/wordpress-cookie-script
- api.virtualjog.hu/api/v1/wordpress-document-list
- api.virtualjog.hu/api/v1/wordpress-valid-domains

## Authorize ( api.virtualjog.hu/api/v1/wordpress-authorize )
- Method: POST
- Parameters:
  - access_token: string
- Description: Authorize the user with the given access token
- Response: 
  - id: int (user id)
  - name: string (user name)
  - packages: array
    - name: string (package name)
    - slug: string (package slug)
    - subPackage: string (sub package name)
    - subPackageSlug: string (sub package slug)
    - subscriptionEndDate: string (subscription end date)
    - active: bool (is package active)

## Cookie Script ( api.virtualjog.hu/api/v1/wordpress-cookie-script )
- Method: POST
- Parameters:
  - access_token: string (user access token)
  - domain: string (current domain name)
- Description: Get the cookie script for the given domain
- Response: 
  - script: string (cookie script)

## Document List ( api.virtualjog.hu/api/v1/wordpress-document-list )
- Method: POST
- Parameters:
  - access_token: string (user access token)
- Description: Get the list of documents
- Response: 
  - documents: array
    - id: int (document id)
    - name: string (document name)
    - slug: string (document slug)
    - lastVersion: int (last version number)
    - embedUrl: string (embed url)
    - acceptedAt: string (accepted date)
    - createdAt: string (created date)
    - updatedAt: string (updated date)

## Valid Domains ( api.virtualjog.hu/api/v1/wordpress-valid-domains )
- Method: POST
- Parameters:
  - access_token: string (user access token)
- Description: Get the list of valid domains
- Response: 
  - domains: string ( comma separated list of the clients domains ) 
