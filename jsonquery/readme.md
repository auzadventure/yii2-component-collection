## JSON Query creators

#### usage 
1. copy to /models folder
2. set your namespace
3. Add to your query

--

```
Table::find()
  ->andWhere(JsonQuery::objSearch($column,$key,$value))
```
