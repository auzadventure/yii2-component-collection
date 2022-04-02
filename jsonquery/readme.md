## JSON Query Creator

#### usage 
1. copy to /models folder
2. set your namespace
3. Add to your query

---

```
Table::find()
  ->andWhere(JsonQuery::objSearch($column,$key,$value))
```

##objSearch($attr,$key,$value)
For { } single object

- aSearchValue($attr,$value)
For [obj obj]

- aSearchKeyValue($attr,$key,$value)
For [obj,obj] 
