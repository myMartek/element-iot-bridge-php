# Element IoT Bridge

# Class: \Mainova\ElementIoTBridge
### Namespace: \Mainova
---
### Methods
* [public request()](README.md#method_request)
* [public getAll()](README.md#method_getAll)
* [public getStream()](README.md#method_getStream)
---
### Details
* File: ElementIotBridge.php
* Package: Default
* Class Hierarchy:
  * \Mainova\ElementIoTBridge

---
## Methods
<a name="method_request" class="anchor"></a>
#### public request() : Object

```
Static public request(String  $url,   $data = null, String  $method = "GET", array  $options = []) : Object
```

**Summary**

Send a request to Element-IoT with optional HTTP Data body

**Details:**
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>String</code> | $url  | API URL of Element-IoT |
| <code>Object</code> | $data  | Optional POST Data |
| <code>String</code> | $method  | HTTP method to use for the request |
| <code>array</code> | $options  | Guzzle Client options used for the request |

**Returns:** Object - URL return data or null

<a name="method_getAll" class="anchor"></a>
#### public getAll() : Array

```
Static public getAll(String  $url) : Array
```

**Summary**

Get all data points iterating over all pages

**Details:**
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>String</code> | $url  | API URL of Element-IoT |

**Returns:** Array - of data points

<a name="method_getStream" class="anchor"></a>
#### public getStream() : Array

```
Static public getStream(String $url) : Array
```

**Summary**

Get all data points using Element-IoTs Streaming API

**Details:**
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>String</code> | $url  | API URL of Element-IoT |

**Returns:** Array - of data points
